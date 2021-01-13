<template>
  <nb-container v-if="dataIsReady">
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{ flex: 1 }">
        <nb-button transparent :on-press="goBack">
          <nb-icon name="arrow-back" />
        </nb-button>
      </nb-left>
      <nb-body :style="{ flex: 1 }">
        <nb-title>{{ Client.firstname }} {{ Client.lastname }}</nb-title>
      </nb-body>
      <nb-right :style="{ flex: 1 }">
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content padder>
      <nb-card>
        <nb-card-item   >
          <nb-picker
            mode="dialog"
            placeholder="Kies een document"
            :selectedValue="selectedDoc"
            :onValueChange="onDocChange"
          >
            <item v-for="filetype in FileTypes" :key="filetype.value" :label="filetype.label" :value="filetype.value" />
          </nb-picker>
        </nb-card-item>
        <nb-card-item v-if="selectedDoc == 10">
          <nb-body
            :style="{ flex: 1, justifyContent: 'center', alignItems: 'center' }"
          >
            <nb-button
              rounded
              info
              :style="{ alignSelf: 'center', backgroundColor: '#0078ae' }"
              :on-press="getCamera"
            >
              <nb-icon active name="ios-add" />
            </nb-button>
            <nb-text>{{ $root.lang.t('upload') }}</nb-text>
          </nb-body>
        </nb-card-item>
        <nb-card-item v-if="displayThumbnail" >
          <nb-button
            transparent
            :on-press="largerImage"
            :style="{ flex: 1, justifyContent: 'center', alignSelf: 'center' }"
          >
            <nb-thumbnail :source="photo" />
          </nb-button>
        </nb-card-item>
        <nb-card-item v-if="selectedDoc == 10"
          :style="{ flex: 1, justifyContent: 'center', alignItems: 'center' }"
        >
          <nb-badge :style="{ height: 'auto' }">
            <nb-text :style="{ fontSize: 14, padding: 5 }">{{
              $root.lang.t('upload_message')
            }}</nb-text>
          </nb-badge>
        </nb-card-item>
        <nb-card-item>
          <nb-item regular>
            <nb-input v-model="title" placeholder="Vul een titel in" />
          </nb-item>
        </nb-card-item>
        <nb-button class="btns" full :on-press="sendFile">
          <nb-text>{{ $root.lang.t('send') }}</nb-text>
        </nb-button>
      </nb-card>
      <nb-grid :style="{ marginTop: 10 }">
        <nb-col>
          <nb-button
            full
            class="btns"
            :on-press="() => detailPage(Client.id, 'Debts')"
          >
            <nb-text>{{ $root.lang.t('debts') }}</nb-text>
          </nb-button>
        </nb-col>
        <nb-col>
          <nb-button
            full
            class="btns"
            :on-press="() => detailPage(Client.id, 'FormsConsultant')"
          >
            <nb-text>{{ $root.lang.t('forms') }}</nb-text>
          </nb-button>
        </nb-col>
      </nb-grid>
      <nb-col>
        <nb-button
          full
          class="btns"
          :on-press="() => detailPage(Client.id, 'OtherDocsConsultant')"
        >
          <nb-text>{{ $root.lang.t('other_documents') }}</nb-text>
        </nb-button>
      </nb-col>
      <nb-col>
        <nb-button
          full
          class="btns"
          :on-press="() => detailPage(Client.id, 'CollectorConsultant')"
        >
          <nb-text>{{ $root.lang.t('creditors_documents') }}</nb-text>
        </nb-button>
      </nb-col>
    </nb-content>
    <modal v-if="displayCam">
      <camera
        class="camera"
        v-if="!displayPreview"
        :type="this.type"
        ref="useCamera"
        :style="{ flex: 1, justifyContent: 'flex-end', alignItems: 'center' }"
      >
        <nb-button
          info
          rounded
          :on-press="takePicture"
          :style="{
            justifyContent: 'center',
            alignSelf: 'center',
            width: 80,
            height: 80,
          }"
        >
          <nb-icon name="camera" />
        </nb-button>
        <nb-button
          warning
          rounded
          :on-press="cancelCamera"
          :style="{
            justifyContent: 'center',
            alignSelf: 'flex-end',
            marginBottom: 20,
            marginRight: 20,
          }"
        >
          <nb-icon name="close" />
        </nb-button>
      </camera>
      <image-background
        class="camera"
        :source="photo"
        v-if="displayPreview"
        :style="{
          flex: 1,
          backgroundColor: 'transparent',
          justifyContent: 'flex-end',
          alignItems: 'center',
        }"
      >
        <view :style="{ flexDirection: 'row', marginBottom: 10 }">
          <nb-button
            success
            rounded
            :on-press="confirmPhoto"
            :style="{ marginRight: 5 }"
          >
            <nb-icon name="checkmark" />
          </nb-button>
          <nb-button
            danger
            rounded
            :on-press="cancelPreview"
            :style="{ marginLeft: 5 }"
          >
            <nb-icon name="close" />
          </nb-button>
        </view>
      </image-background>
    </modal>
    <modal v-if="displayLarge">
      <image-background
        class="camera"
        :source="photo"
        :style="{
          flex: 1,
          backgroundColor: 'transparent',
          justifyContent: 'flex-end',
          alignItems: 'center',
        }"
      >
        <view :style="{ marginBottom: 10 }">
          <nb-button
            danger
            rounded
            :on-press="removeLargerImage"
            :style="{ justifyContent: 'center', alignSelf: 'center' }"
          >
            <nb-icon name="close" />
          </nb-button>
        </view>
      </image-background>
    </modal>
    <nb-footer>
      <footer-nav
        :style="{ backgroundColor: '#0078ae' }"
        activeBtn="docs"
      ></footer-nav>
    </nb-footer>
  </nb-container>
  <nb-spinner color="#0078ae" v-else />
</template>

<script>
import { Picker } from 'native-base';
import FooterNav from '../../included/Footer';
import * as Permissions from 'expo-permissions';
import { Camera } from 'expo-camera';
import Modal from 'react-native-modal';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Toast } from 'native-base';
import * as ImageManipulator from 'expo-image-manipulator';

export default {
  props: {
    navigation: {
      type: Object,
    },
    user: {},
  },
  data() {
    return {
      selectedDoc: '0',
      hasCameraPermission: false,
      type: Camera.Constants.Type.back,
      photo: null,
      displayPreview: false,
      displayCam: false,
      displayThumbnail: false,
      finalPic: null,
      displayLarge: false,
      Client: {},
      dataIsReady: false,
      title: '',
			file: {},
			selectedDocName:'',
			FileTypes: [
				{label:"Kies een document", value:"0" },
				{label:"1.0 Inschrijfform", value:"1"} ,
				{label:"Contracten met Client", value:"2"},
				{label:"1.1 1.2 Akte van Cessie", value:"3"},
				{label:"1.3 MachtigingSysteem", value:"4"},
				{label:"1.4 Machtigingsformulier auto Incasso", value:"5"} ,
				{label:"1.5 Schuldhulp contract", value:"6"} ,
				{label:"1.6 Stabilisatie Overeenkomst", value:"7"} ,
				{label:"1.7 Akte_van verpanding", value:"8" },
				{label:"1.8 Volmacht verstrekt door cliënt EU", value:"9"}, 
				{label:"overige documenten(foto id of passport)", value:"10"} 
			]
    };
  },
  created() {
    this.clientData();
  },
  components: { FooterNav, Camera, Item: Picker.Item },
  methods: {
    clientData: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch(
          `http://api.arsus.nl/consultant/client?id=${this.navigation.getParam(
            'clientID'
          )}`,
          {
            method: 'GET',
            headers: {
              accept: 'application/json',
              'Content-Type': 'application/json',
              Authorization: `Bearer ${this.user.token}`,
            },
          }
        );

        let responseJson = await response.json();
        if (responseJson.success) {
          this.Client = responseJson.results;
          this.dataIsReady = true;
        } else {
          console.log(responseJson.results);
        }
      } catch (error) {
        console.error(error);
      }
    },

    createFormData: function (file, body) {
      let data = new FormData();

      data.append('file', {
        uri:
          Platform.OS === 'android'
            ? this.finalPic.uri
            : this.finalPic.uri.replace('file://', ''),
        type: 'image/jpeg',
        name: this.title, 
      });

      Object.keys(body).forEach((key) => {
        data.append(key, body[key]);
      });
      return data;
    },
    uploadFile: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }
			try {
				let response = await fetch('http://api.arsus.nl/consultant/doc/add', {
					method: 'POST',
					headers: {
						Accept: 'application/json',
						'Content-type': 'multipart/form-data',
						Authorization: `Bearer ${this.user.token}`,
					},
					body: this.createFormData(this.finalPic, {
						title: this.title,
						client_id: this.navigation.getParam('clientID'),
						template_id: 0,
					}),
				});

				let responseJson = await response.json();

				if (responseJson.success) {
					Toast.show({
						text: 'file uploaded',
					});
				} else {
					console.log(responseJson);
				}
			} catch (error) {
				console.error(error);
			}
		},
		uploadDoc: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }
			try {
				let response = await fetch('http://api.arsus.nl/consultant/doc/add', {
					method: 'POST',
					headers: {
						Accept: 'application/json',
						'Content-type': 'application/json',
						Authorization: `Bearer ${this.user.token}`,
					},
					body: JSON.stringify({
						title: this.title ? this.title : this.selectedDocName,
						client_id: this.navigation.getParam('clientID'),
						template_id:this.selectedDoc
					}),
				});

				let responseJson = await response.json();

				if (responseJson.success) {
				Toast.show({
					text: 'file uploaded',
				});
				} else {
					console.log(responseJson);
				}
			} catch (error) {
				console.error(error);
			}
		},
		validateTitle: function(){
			Toast.show({
				text: 'vul een titel in',
				buttonText: 'ok',
			});
		},
    goBack: function () {
      this.navigation.goBack();
    },
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
    onDocChange: function (value,index) {
			this.selectedDoc = value;
			this.selectedDocName = this.FileTypes[index].label;
    },
    getCamera: function () {
      Permissions.askAsync(Permissions.CAMERA)
        .then((status) => {
          this.hasCameraPermission = status.status == 'granted' ? true : false;
          this.displayCam = true;
        })
        .catch((err) => {
          console.log(err);
          alert(err);
        });
    },
    takePicture: async function () {
      if (this.$refs['useCamera']) {
        let pic = await this.$refs['useCamera'].takePictureAsync();
        let resizedPhoto = await ImageManipulator.manipulateAsync(
          pic.uri,
          [],
          { compress: 0.7, format: 'jpeg', base64: false }
				);
        this.photo = { uri: resizedPhoto.uri };
      }
      this.displayPreview = true;
    },
    cancelCamera: function () {
      this.displayCam = false;
    },
    cancelPreview: function () {
      this.displayPreview = false;
    },
    confirmPhoto: function () {
      this.displayPreview = false;
      this.displayCam = false;
      this.finalPic = this.photo;
      this.displayThumbnail = true;
    },
    largerImage: function () {
      this.displayLarge = true;
    },
    removeLargerImage: function () {
      this.displayLarge = false;
    },
    sendFile: function () {
			if (this.selectedDoc >= 1 && this.selectedDoc < 10) {
				this.uploadDoc();
			} else if(this.selectedDoc >= 1 && this.selectedDoc == 10) {
				this.uploadFile();
			}else {
				Toast.show({
					text: 'kies een document',
					buttonText: 'ok',
				});
			}
    },
    detailPage: function (id, pageName) {
      this.navigation.navigate(pageName, {
        id: id,
      });
    },
  },
};
</script>

<style>
.btns {
  padding: 20px;
  background-color: #0078ae;
  margin: 7px;
  align-items: center;
  border-radius: 10px;
  justify-content: center;
}

.camera {
  flex: 1;
}
</style>
