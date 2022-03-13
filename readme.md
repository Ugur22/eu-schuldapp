
# EU-schuld-app
This app is a simplified version of [admin.eu-schuldhulp ](https://github.com/EU-IT/consulent) 
primarily meant to be used by clients to upload files, photos and view their own account info. This app does not include the admin and most consultant features of the admin.eu-schuldhulp platform.


## Installation 

### Setup with Vue Native CLI

```
 npm install --global vue-native-cli
```

#### Install Expo CLI globally
```
npm install --global expo-cli
```

### Install eu-schuldapp with npm

``` 
  cd eu-schuldapp
  npm install
```

### Run the app

* **Run on iOS**
  * Opt #1:
    * Run `npm start` in your terminal
    * Scan the QR code in your Expo app
  * Opt #2:
    * Run `npm run ios` in your terminal

- **Run on Android**
  * Opt #1:
    * Run `npm start` in your terminal
    * Scan the QR code in your Expo app
  * Opt #2:
    * Make sure you have an `Android emulator` installed and running
    * Run `npm run android` in your terminal

    [more info on installing and setting up Vue-Native](https://vue-native.io/docs/installation.html)

### debug in production
```
expo start --no-dev --minify
```
    
## Tech Stack

**Client:** Vue-Native, React-Native, Expo, native-base

**Server([eu-schuldhulp-api](https://github.com/EU-IT/eu-schuldhulp-api))**: Lumen
  
## Features

### clients
- Manage appointments(create and edit)
- view Debts
- Manage documents and files(Add,view and download)
- Sign documents(add signature)
- Receive notifications
- view account details
- view creditors

### consultant
- Manage clients(view and change client status)
- Manage debts(view and change debt status)
- Sign documents(add signature)
- Manage appointments(create and edit)
- view creditors
- Manage documents and files(Add,view and download)
  
## Authors

- [@Ugur22](https://github.com/Ugur22)

