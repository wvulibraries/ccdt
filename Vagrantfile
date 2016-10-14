# -*- mode: ruby -*-
# vi: set ft=ruby :

PROJECT_NAME = "CSS"
API_VERSION  = "2"

Vagrant.configure(API_VERSION) do |config|
	config.vm.define PROJECT_NAME, primary: true do |config|
		config.vm.provider :virtualbox do |vb|
			vb.name = PROJECT_NAME
		end

		config.vm.box = "bento/centos-7.2"
    config.vm.network "private_network", ip: "192.168.5.10"
		config.vm.network :forwarded_port, guest: 80, host: 8066
		config.vm.provision "shell", path: "bootstrap.sh"
		config.vm.synced_folder "./src", "/var/www/html", :mount_options => ["dmode=777", "fmode=777"]
	end
end
