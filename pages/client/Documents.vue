<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('file')" :method="goBack" />
    <nb-content padder >
      <!-- <nb-card>
        <nb-card-item>
          <nb-picker
            mode="dialog"
            placeholder="Type document"
            :selectedValue="selectedDoc"
            :onValueChange="onDocChange"
          >
            <item label="Type document" value="0" />
            <item label="Formulieren" value="form" />
            <item label="Overige documenten" value="others" />
            <item label="Schulden" value="debt" />
          </nb-picker>
        </nb-card-item>
        <nb-card-item>
          <nb-body :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
            <nb-button rounded info :style="{ alignSelf: 'center',backgroundColor:'#0078ae' }" :on-press="getCamera">
              <nb-icon active name="ios-add" />
            </nb-button>
            <nb-text>{{ $root.lang.t('upload') }}</nb-text>
          </nb-body> 
        </nb-card-item>
        <nb-card-item v-if="displayThumbnail">
            <nb-button transparent :on-press="largerImage" :style="{ flex:1, justifyContent: 'center', alignSelf: 'center' }">
              <nb-thumbnail :source="photo" />
            </nb-button>
        </nb-card-item>
        <nb-card-item :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
          <nb-badge :style="{ height:'auto' }">
            <nb-text :style="{ fontSize: 14, padding:5 }">{{ $root.lang.t('upload_message') }}</nb-text>
          </nb-badge>
        </nb-card-item>
        <nb-card-item>
          <nb-item regular>
            <nb-input placeholder="Naam schuldeiser" />
          </nb-item>
        </nb-card-item>
        <nb-button class="btns" full :on-press="sendFile">
          <nb-text>{{ $root.lang.t('send') }}</nb-text>
        </nb-button>
      </nb-card> -->
      <nb-grid :style="{ marginTop: 20}" v-if="clientStatus >= 5 && dataIsReady">
        <nb-col v-if="clientStatus >= 5">
          <nb-button full class="btns" :on-press="() => goToPage('DebtList')">
            <nb-text>{{ $root.lang.t('debts') }}</nb-text>
          </nb-button>
        </nb-col>
        <nb-col>
          <nb-button full class="btns" :on-press="() => goToPage('FormList')">
            <nb-text>{{ $root.lang.t('forms') }}</nb-text>
          </nb-button>
        </nb-col>
      </nb-grid>
      <nb-col>
        <nb-button full class="btns" :on-press="() => goToPage('DocsCollector')">
          <nb-text>{{ $root.lang.t('creditors_documents') }}</nb-text>
        </nb-button>
      </nb-col>
      <nb-col>
        <nb-button full class="btns" :on-press="() => goToPage('DocsOthers')">
          <nb-text>{{ $root.lang.t('other_documents') }}</nb-text>
        </nb-button>
      </nb-col>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="docs"></footer-nav>
    </nb-footer>
    <!-- <modal v-if="displayCam">
      <camera class="camera" v-if="!displayPreview" :type="this.type" ref="useCamera" :style="{ flex: 1,  justifyContent: 'flex-end', alignItems: 'center' }">
        <nb-button info rounded :on-press="takePicture" :style="{ justifyContent: 'center', alignSelf: 'center', width:80, height: 80 }">
          <nb-icon name="camera" />
        </nb-button>
        <nb-button warning rounded :on-press="cancelCamera" :style="{ justifyContent: 'center', alignSelf: 'flex-end', marginBottom:20, marginRight:20 }">
          <nb-icon name="close" />
        </nb-button>
      </camera>
      <image-background class="camera" :source="photo" v-if="displayPreview" :style="{ flex: 1,  backgroundColor: 'transparent', justifyContent: 'flex-end', alignItems: 'center' }">
        <view :style="{ flexDirection:'row', marginBottom: 10 }">
          <nb-button success rounded :on-press="confirmPhoto" :style="{ marginRight:5 }">
            <nb-icon name="check" />
          </nb-button>
          <nb-button danger rounded :on-press="cancelPreview" :style="{ marginLeft:5 }">
            <nb-icon name="close" />
          </nb-button>
        </view>
      </image-background>
    </modal>
    <modal v-if="displayLarge">
      <image-background class="camera" :source="photo" :style="{ flex: 1,  backgroundColor: 'transparent', justifyContent: 'flex-end', alignItems: 'center' }">
        <view :style="{ marginBottom: 10 }">
          <nb-button danger rounded :on-press="removeLargerImage" :style="{ justifyContent: 'center', alignSelf: 'center' }">
            <nb-icon name="times" />
          </nb-button>
        </view>
      </image-background>
    </modal> -->
  </nb-container>
</template>

<script>
  import { Picker } from "native-base";
  import FooterNav from '../../included/Footer';
import Header from '../../included/Header';
  import * as Permissions from 'expo-permissions';
  import { Camera } from 'expo-camera';
	import {fetchData} from "../utils/fetch";

  export default {
    props: {
      navigation: {
        type: Object
      },
    },
    data() {
      return {
				clientData: {},
        selectedDoc: '0',
        hasCameraPermission: false,
        type: Camera.Constants.Type.back,
        photo: null,
        displayPreview: false,
        displayCam: false,
        displayThumbnail: false,
        finalPic: null,
        displayLarge: false,
				clientStatus:0,
				dataIsReady: false,
      };
    },
		mounted() {
			fetchData(`client`,this.$root.user.token).then(val => {
				this.dataIsReady = true;
				this.clientData = val;
				this.clientStatus = this.clientData.status.id;
			});
		},
    components: { FooterNav, Camera, Item: Picker.Item,Header },
    methods: {
      goBack: function () {
        this.navigation.goBack();
      },
      goToPage: function (page) {
        this.navigation.navigate(page);
      },
      onDocChange: function (value) {
        this.selectedDoc = value;
      },
      getCamera: function () {
        Permissions.askAsync(Permissions.CAMERA).then(status => {
          this.hasCameraPermission = status.status == "granted" ? true : false;
          this.displayCam = true;
        }).catch((err)=>{
            console.log(err);
          alert(err);
        });
      },
      takePicture: async function () {
        if (this.$refs['useCamera']) {
          let pic = await this.$refs['useCamera'].takePictureAsync();
          this.photo = {uri: pic.uri};
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
      }
    }
  }
</script>
<style>
.btns {
  padding:10px;
  background-color:#0078ae;
  margin:10px;
  align-items: center;
  border-radius: 10px;
  justify-content: center;
}

.camera {
  flex: 1;
}
</style>
