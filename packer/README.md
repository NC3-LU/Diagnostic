About this Packer Folder
========================

This folder should help you to generate à virtual machine with the help of Packer (https://www.packer.io/)
It could help you to have a VM as fast as possible, but with the possibility to modify it at your will. 
You can easily change your installation in the [diagnostic_installer.sh](scripts/diagnostic_installer.sh).

> WARNING
>
> If you try to launch the packer of the diagnostic.json without editing nothing, you should probably have an error while launching for the 
> first time the VM. It is configure to have an Host-Only adapter called "YourHostOnlyAdapter". You should probably configure a new 
> Host-Only adapter, with the help of the [quick-start guide](../README.md)
