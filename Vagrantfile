# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

# The name we are giving this project
PROJECT_NAME = "rockefeller-css"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
	config.vm.define PROJECT_NAME, primary: true do |config|
		config.vm.provider :virtualbox do |vb|
			vb.name = PROJECT_NAME
		end

		# Every Vagrant virtual environment requires a box to build off of.
		config.vm.box = "centos6.4"

		# The url from where the 'config.vm.box' box will be fetched if it
		# doesn't already exist on the user's system.
		config.vm.box_url = "https://github.com/2creatives/vagrant-centos/releases/download/v0.1.0/centos64-x86_64-20131030.box"

		config.vm.network :forwarded_port, guest: 80, host: 8056
		config.vm.network :forwarded_port, guest: 10000, host: 10002

		config.vm.network "private_network", ip: "192.168.56.6"

		config.vm.provision "shell", path: "bootstrap.sh"
	end
end
