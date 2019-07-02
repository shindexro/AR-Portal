===========================IMPORTANT===========================
The ARPortal directory is an Unity project folder

The mobile app only works on phone that is supported by Google ARCore

The portal download feature of the application would not work if the
web server is shut down, or after the AWS server instance restarts 
because the server IP would change.

Please edit the web server IP address in
ARPortal\Assets\Scripts\UI\MapDownloader.cs