<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('forms')" :method="goBack" />
    <nb-content>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="form in clientForms" :key="form.id">
          <nb-left>
						<nb-button v-if="!form.main" iconRight transparent>
							<nb-icon  class="text" name="document" />
						</nb-button>
						<nb-button v-else iconRight transparent >
							<nb-icon  class="text" name="mail" />
						</nb-button>
            <nb-text class="text">{{form.title}}</nb-text>
          </nb-left>
					<nb-right :style="{flexDirection:'row'}">
						<nb-button iconRight transparent :disabled="buttonOff" :on-press="() => downloadPDF(form.id,form.client_id,form.title)" >
							<nb-icon  class="text" name="download" />
						</nb-button>
						<nb-button v-if="Platform.OS === 'android'" iconLeft transparent :disabled="buttonOff" :on-press="() => showPDF(form.id,form.client_id,form.title)" >
							<nb-icon  class="text" name="eye" />
						</nb-button>
          </nb-right>
        </nb-list-item>
      </nb-list>
      <nb-spinner color="#0078ae" v-else />
    </nb-content>
    <nb-footer>
      <footer-nav
        :style="{ backgroundColor: '#0078ae' }"
        activeBtn="docs"
      ></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<style>
.text {
    color: #0078ae;
}
</style>
<script>
import FooterNav from '../../included/Footer';
import Header from '../../included/Header';
import {formatDate} from "../utils/dates";
import {Platform} from 'react-native';
import {fetchData} from "../utils/fetch";
import * as FileSystem from 'expo-file-system';
import * as IntentLauncher from 'expo-intent-launcher';
import * as Permissions from 'expo-permissions';
import * as MediaLibrary from 'expo-media-library';
import { Toast } from 'native-base';
import * as Sharing from 'expo-sharing';

export default {
  props: {
    navigation: {
      type: Object,
    },
    user: {},
  },
  data() {
    return {
      clientForms: {},
      dataIsReady: false,
			formatDate,
			buttonOff: false,
			formLoaded:false,
			Platform
    };
  },
  created() {
	},
	mounted() {
		fetchData(`client/docs/forms`,this.$root.user.token).then(val => {
		this.dataIsReady = true;
			this.clientForms = val;
			});
	},
  components: { FooterNav,Header},
  methods: {
	showPDF: async function(id,clientID,title){
		this.buttonOff = true;
		setTimeout(() => this.buttonOff = false, 2000);
		const { status } = await Permissions.askAsync(Permissions.CAMERA_ROLL);
		let that = this;
			let options = {			
					headers: {
						accept: 'application/json',
						'Content-Type': 'application/json',
						'Authorization': `Bearer ${this.$root.user.token}`
			}}

			if (status === "granted") {
						FileSystem.downloadAsync(`http://api.arsus.nl/document/pdf-file?client_id=${clientID}&document_id=${id}`,
						FileSystem.documentDirectory + `${title}.pdf`,options
				).then(async({ uri,status }) => {

						if(this.Platform.OS === 'android'){
							const asset = await MediaLibrary.createAssetAsync(uri);
							await MediaLibrary.createAlbumAsync("Download", asset, false);
							Toast.show({
								text: `uw document is succesvol opgeslagen. Ga naar uw bestandsbeheer/telefoonopslag voor uw download`,
								buttonText: 'ok',
								position: "center",
								duration: 3000,
								type: "success", 
							});
						}else {
							Sharing.shareAsync(uri);
						}
					
				});
			}
			that.formLoaded = true;
		},
	downloadPDF: async function(id,clientID,title){
		this.buttonOff = true;
		setTimeout(() => this.buttonOff = false, 2000);
		const { status } = await Permissions.askAsync(Permissions.CAMERA_ROLL);
		let that = this;
			let options = {			
					headers: {
						accept: 'application/json',
						'Content-Type': 'application/json',
						'Authorization': `Bearer ${this.$root.user.token}`
			}}

		 if (status === "granted") {
					FileSystem.downloadAsync(`http://api.arsus.nl/document/pdf-file?client_id=${clientID}&document_id=${id}`,
					FileSystem.documentDirectory + `${title}.pdf`,options
			).then(async({ uri,status }) => {
						const asset = await MediaLibrary.createAssetAsync(uri);
      			await MediaLibrary.createAlbumAsync("Download", asset, false);
				 	Toast.show({
						text: `uw document is succesvol opgeslagen. Ga naar uw bestandsbeheer/telefoonopslag voor uw download`,
						buttonText: 'ok',
						position: "center",
						duration: 3000,
						type: "success", 
					});
			});
		 }
			that.formLoaded = true;
		},
    goBack: function () {
      this.navigation.goBack();
    },
  },
};
</script>
