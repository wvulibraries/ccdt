# -*- mode: ruby -*-
# vi: set ft=ruby :

PROJECT_NAME = "ProjectCSS"
API_VERSION  = "2"

Vagrant.configure(API_VERSION) do |config|
	config.vm.define PROJECT_NAME, primary: true do |config|
		config.vm.provider :virtualbox do |vb|
			vb.name = PROJECT_NAME
		end

		# Public Centos-7 box
		config.vm.box = "bento/centos-7.2"
		config.vm.box_version = "2.2.9"

		# WVU Centos box
		# config.vm.box = "CentOS7"
		# config.vm.box_url = "https://vagrant.lib.wvu.edu/CentOS7.box"

		config.vm.network "private_network", ip: "192.168.5.10"
		config.vm.network :forwarded_port, guest: 80, host: 8066
		config.vm.provision "shell", path: "bootstrap.sh"
		config.vm.synced_folder "./src", "/var/www/html", :mount_options => ["dmode=777", "fmode=777"]
	end
end
