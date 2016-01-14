<!DOCTYPE html>
<html>
<body>
Notes<br />
-----<br />
This tool is a netfilter frontend. It reads rulesets from<br />
configuration files and can be used to setup iptables rules<br />
for different interfaces using specified network definitions<br />
from /etc/network/interfaces.<br />
<br />
Configuration structure<br />
-----------------------<br />
rulesets = ruleset*<br />
ruleset = rule*<br />
rule = attributes*<br />
<br />
Attributes<br />
----------<br />
active - define a a ruleset/rule as active<br />
atype - address type passed onto netfilter<br />
chain - specify to store a rule in, chains will be created if they do not exist<br />
condition - specify a condition on any internal variable <br />
destination - destination address<br />
dports - destination ports<br />
if-in - input nterface<br />
if-out - output interface<br />
log - enable logging<br />
log-level - set log level to e.g. combine it with rsyslogd config/filters<br />
log-prefix - sets log prefix<br />
name - name rulesets to access them on initition<br />
protocol - define the protcol (tcp,udp,icmp,all)<br />
ruleset - call another ruleset if it exists<br />
source - source address <br />
sports - source ports<br />
state - define acceptable states<br />
table - define a table to place a rule into (default=filter)<br />
target - chain targets, e.g. ACCEPT,MASQUERADE<br />
<br />
Modifying CHANGELOG<br />
-------------------<br />
$ export DEBFULLNAME="Marc Bredt"; export DEBEMAIL="marc.bredt@gmail.com"<br />
$ dch -c trunk/CHANGELOG --package kaese --distribution stable --create -v 0.0.2<br />
$ dch -c trunk/CHANGELOG -r<br />
$ dch -c trunk/CHANGELOG -i<br />
<br />
<br />
Providing access to iptables logfiles for statistics<br />
----------------------------------------------------<br />
* via rsyslogd, user group kaese got read access<br />
<br />
  $ cat /etc/rsyslod.d/iptables.conf<br />
  :msg, contains, "iptables: " action(type="omfile" DirCreateMode="0750" <br />
                                      DirOwner="root" DirGroup="kaese" <br />
                                      FileCreateMode="0640" FileOwner="root" <br />
                                      FileGroup="kaese" <br />
                                      File="/var/log/kaese/iptables.log")<br />
  :msg, contains, "iptables: " ~<br />
  kern.=debug action(type="omfile" DirCreateMode="0750" DirOwner="root" <br />
                     DirGroup="kaese" FileCreateMode="0640" FileOwner="root" <br />
                     FileGroup="kaese" File="/var/log/kaese/iptables.log")<br />
  kern.=debug ~<br />
<br />
  $ ll /var/log/kaese/<br />
  123   4 drwxr-x---  2 root kaese   4096 Jan  3 15:30 .<br />
  456  12 drwxr-xr-x 20 root root   12288 Jan  3 15:30 ..<br />
  789 164 -rw-r-----  1 root kaese 161397 Jan  3 16:22 iptables.log<br />
<br />
Providing access to iptables command for statistics<br />
---------------------------------------------------<br />
* via sudo,calife,chiark-really<br />
  * bind a limited amount of parameters for iptables onto sudo calls<br />
  <br />
  $ cat /etc/sudoers<br />
  #<br />
  # This file MUST be edited with the 'visudo' command as root.<br />
  # <br />
  # Please consider adding local content in /etc/sudoers.d/ instead of<br />
  # directly modifying this file.<br />
  #<br />
  # See the man page for details on how to write a sudoers file.<br />
  #<br />
  Defaults        env_reset<br />
  Defaults        mail_badpass<br />
  Defaults        secure_path="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"<br />
<br />
  # Host alias specification<br />
  Host_Alias HOST_ALIAS_KAESE = localhost<br />
<br />
  # User alias specification<br />
  User_Alias USER_ALIAS_KAESE = %kaese<br />
<br />
  # Cmnd alias specification<br />
  Cmnd_Alias CMND_ALIAS_KAESE_IPT_LIST_FILTER = /sbin/iptables -t filter -nv --list, /sbin/iptables -t filter -nv --list [[\:alpha\:]]*<br />
  Cmnd_Alias CMND_ALIAS_KAESE_IPT_LIST_NAT = /sbin/iptables -t nat -nv --list, /sbin/iptables -t nat -nv --list [[\:alpha\:]]*<br />
<br />
  # User privilege specification<br />
  root    ALL=(ALL:ALL) ALL<br />
<br />
  # Allow members of group sudo to execute any command<br />
  %sudo   ALL=(ALL:ALL) ALL<br />
  USER_ALIAS_KAESE        HOST_ALIAS_KAESE = NOPASSWD: CMND_ALIAS_KAESE_IPT_LIST_FILTER, CMND_ALIAS_KAESE_IPT_LIST_NAT<br />
<br />
  # See sudoers(5) for more information on "#include" directives:<br />
<br />
  #includedir /etc/sudoers.d<br />
<br />
* via php's exec/escapeshell*/shell_exec/system etc.<br />
<br />
</body>
</html>

