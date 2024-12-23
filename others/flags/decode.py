#!/bin/python3
from base64 import b64decode
from base64 import b64encode
import requests

def bitFlip( pos, bit, data):
    raw = b64decode(data)
    list1 = list(raw)
    list1[pos] = chr(ord(list1[pos])^bit)
    raw = ''.join(list1)
    return b64encode(raw)

#ck = "UXVDRDhEMmNrbTFCV25jbzdheFBjbHNmOWErZnNJdnY5Nk5pUkVNTkVXYUdRK0FVSk9tTGtRT3h1a0dWSDJrbmNHSUxsRTlNR2FZZFJaZ3RRb09EdngyUnd6L3FlbCtPSmZjbnJUVE5pWnVVUHNDQ1lJdFkzbTI4N29NWWxBRU4="
ck = "UkZFczJzVzN1ODdqTkFyYlR4OGhuOEZ2dzZGTzFsa1RGNERyczFWNlZDVGZFZHN5N3pTZk1YdG1wZThMVmg2cnczRW5WbU1jbDRuOG1mdUtQcnE2NTBPdUJtT2g5VzdZUG1UWW1uV0hHazBRYUJpaHpkc1VtMkNoZEtVMk1TNzc="
for i in range(128):
  for j in range(128):
    c = bitFlip(i, j, ck)
    cookies = {'auth_name': c}
    r = requests.get('http://mercury.picoctf.net:43275/', cookies=cookies)
    if "picoCTF{" in r.text:
      print(r.text)
      break
