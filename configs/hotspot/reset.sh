#!/bin/bash
service  NetworkManager start
service wpa_supplicant start
iptables --flush

