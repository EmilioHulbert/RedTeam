#!/bin/bash

#Create hotspot and forward traffic to interface eth0
#forward commands #make sure you are sudo
#ip_forward
#ip set up
#ifconfig  wlan0 10.0.0.1 up
#service wpa_supplicant stop
ifconfig wlan0 192.168.28.1 up
#ifconfig wlan0 up
echo 1 > /proc/sys/net/ipv4/ip_forward
iptables -A FORWARD -i wlan0 -o eth0 -j ACCEPT
iptables -A FORWARD -i eth0 -o wlan0  -m state --state ESTABLISHED,RELATED -j ACCEPT
iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE

