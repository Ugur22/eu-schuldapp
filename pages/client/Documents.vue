<template>
  <nb-container v-if="dataIsReady">
    <header :pageTitle="$root.lang.t('file')" :method="goBack" />
    <nb-content padder>
      <nb-card>
        <nb-card-item>
          <nb-picker
            :style="{ width: Platform.OS === 'android' ? width - 60 : width - 40 }"
            mode="dialog"
            :placeholder="$root.lang.t('document_type')"
            :selectedValue="selectedDoc"
            :iosIcon="getIosIcon()"
            :onValueChange="onDocChange"
          >
            <item v-for="selection in selections" :key="selection.id" :label="selection.slug" :value="selection.id" />
          </nb-picker>
        </nb-card-item>
        <nb-card-item>
          <nb-body :style="{ flex: 1, justifyContent: 'center', alignItems: 'center'}">
            <nb-button rounded info :style="{ alignSelf: 'center', backgroundColor: '#0078ae', width:150 }" :on-press="getCamera">
              <nb-icon active name="camera" />

            <nb-text>Camera</nb-text>
            </nb-button>
            <nb-button v-if="selectedDocName === 'other'" rounded info :style="{ alignSelf: 'center', backgroundColor: '#0078ae' , marginTop:10, width:150  }" :on-press="pickDocument">
              <nb-icon active name="document" />

            <nb-text>Document</nb-text>
            </nb-button>
            <nb-button rounded info :style="{ alignSelf: 'center', backgroundColor: '#0078ae' , marginTop:10, width:150  }" :on-press="pickImage">
              <nb-icon active name="image" />
            <nb-text>Image</nb-text>
            </nb-button>
          </nb-body>
        </nb-card-item>
        <nb-card-item v-if="displayThumbnail">
          <nb-button v-if="finalPic"
            transparent
            :on-press="largerImage"
            :style="{ flex: 1, justifyContent: 'center', alignSelf: 'center' }"
          >
            <nb-thumbnail :source="finalPic" />
          </nb-button>
        </nb-card-item>
        <nb-card-item :style="{ flex: 1, justifyContent: 'center', alignItems: 'center' }">
          <nb-badge :style="{ height: 'auto' }">
            <nb-text :style="{ fontSize: 14, padding: 5 }">{{ $root.lang.t('upload_message') }}</nb-text>
          </nb-badge>
        </nb-card-item>
        <nb-card-item>
          <nb-item regular>
            <nb-input v-model="title" :placeholder="$root.lang.t('title')" />
          </nb-item>
        </nb-card-item>
        <nb-button class="btns" full :on-press="sendFile">
          <nb-text>{{ $root.lang.t('send') }}</nb-text>
        </nb-button>
      </nb-card>
      <nb-content>
        <nb-grid :style="{ marginTop: 10 }">
          <nb-col>
            <nb-button full class="btns" :on-press="() => detailPage(Client.id, 'DebtList')">
              <nb-text>{{ $root.lang.t('debts') }}</nb-text>
            </nb-button>
          </nb-col>
          <nb-col>
            <nb-button full class="btns" :on-press="() => detailPage(Client.id, 'FormList')">
              <nb-text>{{ $root.lang.t('forms') }}</nb-text>
            </nb-button>
          </nb-col>
        </nb-grid>
        <nb-col>
          <nb-button full class="btns" :on-press="() => detailPage(Client.id, 'DocsOthers')">
            <nb-text>{{ $root.lang.t('other_documents') }}</nb-text>
          </nb-button>
        </nb-col>
        <nb-col>
          <nb-button full class="btns" :on-press="() => detailPage(Client.id, 'DocsCollector')">
            <nb-text>{{ $root.lang.t('creditors_documents') }}</nb-text>
          </nb-button>
        </nb-col>
      </nb-content>
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
          <nb-button success rounded :on-press="confirmPhoto" :style="{ marginRight: 5 }">
            <nb-icon name="checkmark" />
          </nb-button>
          <nb-button danger rounded :on-press="cancelPreview" :style="{ marginLeft: 5 }">
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
      <footer-nav :style="{ backgroundColor: '#0078ae' }" activeBtn="docs"></footer-nav>
    </nb-footer>
  </nb-container>
	 <nb-spinner color="#0078ae" v-else />
</template>

<script>
import { Dimensions, Platform } from 'react-native';
import { Picker, Icon } from 'native-base';
import FooterNav from '../../included/Footer';
import Header from '../../included/Header';
import * as Permissions from 'expo-permissions';
import { Camera } from 'expo-camera';
import { Toast } from 'native-base';
import * as ImageManipulator from 'expo-image-manipulator';
import { fetchData } from '../utils/fetch';
import React from 'react';
import * as ImagePicker from 'expo-image-picker';
import * as DocumentPicker from 'expo-document-picker';

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
      finalDoc: null,
      displayLarge: false,
      Client: {},
      dataIsReady: false,
      width: Dimensions.get('window').width,
      title: '',
      Platform,
      file: {},
      selectedDocName: '',
      selections: {},
    };
  },
  created() {},
  mounted() {
    fetchData(`upload-options`, this.$root.user.token).then(val => {
      this.selections = val.slice(0, 5);
      this.dataIsReady = true;
    });
  },
  components: { FooterNav, Camera, Item: Picker.Item, Header },
  methods: {
    getIosIcon: function () {
      return <Icon name='arrow-down' />;
    },
    pickDocument: async function () {
      let result = await DocumentPicker.getDocumentAsync({
				type: "application/*"
			});
     	
			if (!result.cancelled) {
				this.finalPic = result 
			}
    },
    pickImage: async function () {
   let result = await ImagePicker.launchImageLibraryAsync({
      allowsEditing: false,
    });

    if (!result.cancelled) {
     	this.finalPic = result 
			this.displayThumbnail = true;
    }
  },
    createFormData: function (file, body) {
      let data = new FormData();

      data.append('file', {
        uri: Platform.OS === 'android' ? file.uri : file.uri.replace('file://', ''),
        type: 'image/jpeg',
        name: this.selectedDocName,
      });

      Object.keys(body).forEach(key => {
        data.append(key, body[key]);
      });
      return data;
    },
    uploadImage: async function () {
      if (this.selectedDocName === 'other' && this.title === '') {
        this.validateTitle();
        return;
      }

      try {
        let response = await fetch('http://api.arsus.nl/client/docs/send', {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-type': 'multipart/form-data',
            Authorization: `Bearer ${this.$root.user.token}`,
          },
          body: this.createFormData(this.finalPic, {
            title: this.title ? this.title : this.selectedDocName,
            option: this.selectedDocName,
          }),
        });

        let responseJson = await response.json();

        if (responseJson.success) {
					this.displayThumbnail = false;
          Toast.show({
            text: `${this.$root.lang.t('file_uploaded')}`,
            position: 'center',
            type: 'success',
            duration: 3000,
          });
          this.photo ? this.photo.uri : this.finalPic  = '';
          this.title = '';
        } else {
          console.log(responseJson);
        }
      } catch (error) {
        console.error(error);
      }
    },
    validateTitle: function () {
      Toast.show({
        text: `${this.$root.lang.t('missing_title')}`,
        position: 'center',
        type: 'danger',
        duration: 3000,
      });
    },
    showInfo: function () {
      alert('show info about this page');
    },
    goBack: function () {
      this.navigation.goBack();
    },
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
    onDocChange: function (value, index) {
      this.selectedDoc = value;
      if (Platform.OS === 'android') {
        this.selectedDocName = this.selections[index].slug;
      } else {
        this.selectedDocName = this.selections[index - 1].slug;
      }
    },
    getCamera: function () {
      Permissions.askAsync(Permissions.CAMERA)
        .then(status => {
          this.hasCameraPermission = status.status == 'granted' ? true : false;
          this.displayCam = true;
        })
        .catch(err => {
          console.log(err);
          alert(err);
        });
    },
    takePicture: async function () {
      if (this.$refs['useCamera']) {
        let pic = await this.$refs['useCamera'].takePictureAsync();
        let resizedPhoto = await ImageManipulator.manipulateAsync(pic.uri, [], {
          compress: 0.7,
          format: 'jpeg',
          base64: false,
        });
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
      if (this.finalPic !== null) {
        this.uploadImage();
      } else {
        Toast.show({
          text: `${this.$root.lang.t('missing_upload')}`,
          position: 'center',
          type: 'danger',
          duration: 3000,
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
