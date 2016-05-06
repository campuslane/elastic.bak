echo ">> Update Aptitude"
apt-get update
 
echo ">> Install Java"
apt-get install openjdk-7-jre-headless -y
 
echo ">> Download and install Elasticsearch Public Signing Key"
wget -qO - http://packages.elasticsearch.org/GPG-KEY-elasticsearch | sudo apt-key add -
 
echo ">> Add repository"
echo "deb http://packages.elasticsearch.org/elasticsearch/1.5/debian stable main" > /etc/apt/sources.list.d/elasticsearch.list
 
echo ">> Update Aptitude"
apt-get update
 
echo ">> Install Elasticsearch"
apt-get install elasticsearch
 
echo ">> Set Elasticsearch to run on startup"
update-rc.d elasticsearch defaults 95 10
 
echo ">> Start Elasticsearch server"
/etc/init.d/elasticsearch start