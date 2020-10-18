# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/focal64"
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.manage_guest = true

  config.vm.define "datinglibredb" do |datinglibredb|
    datinglibredb.vm.hostname = "datinglibredb.local"
    datinglibredb.vm.network "private_network", ip: "192.168.0.100"
    datinglibredb.vm.provider "virtualbox" do |v|
      v.memory = 768
      v.name = "datinglibredb"
      v.customize ["modifyvm", :id, "--uart1", "0x3F8", "4"]
      v.customize ["modifyvm", :id, "--uartmode1", "file", File::NULL]
    end
  end

  config.vm.define "datinglibretesting" do |datinglibretesting|
    datinglibretesting.vm.hostname = "datinglibretesting.local"
    datinglibretesting.vm.network "private_network", ip: "192.168.0.101"
    datinglibretesting.vm.provider "virtualbox" do |v|
      v.memory = 768
      v.name = "datinglibretesting"
      v.customize ["modifyvm", :id, "--uart1", "0x3F8", "4"]
      v.customize ["modifyvm", :id, "--uartmode1", "file", File::NULL]
    end
  end

  config.vm.define "datinglibre" do |datinglibre|
    datinglibre.vm.hostname = "datinglibre.local"
    datinglibre.vm.network "private_network", ip: "192.168.0.99"
    datinglibre.vm.provider "virtualbox" do |v|
          v.memory = 768
          v.name = "datinglibre"
          v.customize ["modifyvm", :id, "--uart1", "0x3F8", "4"]
          v.customize ["modifyvm", :id, "--uartmode1", "file", File::NULL]

    end
  end

  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "deploy/sites.yml"
    ansible.inventory_path = "deploy/inventories/staging/hosts"
    ansible.vault_password_file  = "deploy/staging_vault_password"
  end

end
