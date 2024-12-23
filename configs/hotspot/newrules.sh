#killall NetworkManager
#killall wpa_supplicant
#airmon-ng check kill
ifconfig  wlan0 192.168.28.1 up
echo 1 > /proc/sys/net/ipv4/ip_forward
iptables --table nat --append POSTROUTING --out-interface eth0 -j MASQUERADE
iptables --append FORWARD --in-interface wlan0 -j ACCEPT
