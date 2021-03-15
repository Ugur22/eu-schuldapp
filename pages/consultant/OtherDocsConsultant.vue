<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('other_documents')" :method="goBack" />
	<nb-content >
	  <nb-item :style="{ borderColor: '#62B1F6' }">
		<nb-input v-model="searchDocs" :placeholder="$root.lang.t('search')" />
	  </nb-item>
		<nb-item>
				 <nb-text :style="{ display: 'none' }"  class="text">{{getInput}}</nb-text>
			</nb-item>
			<nb-item v-if="clientDocs.length === undefined && dataIsReady" >
					<nb-text class="text">
						geen resultaten gevonden
					</nb-text>
			</nb-item>
	  <nb-list v-if="dataIsReady">
		<nb-list-item v-for="docs in clientDocs" :key="docs.id" :disabled="buttonOff" :on-press="() => detailOther(docs.id,docs.client_id,docs.file.filetype,docs.title)">
		  <nb-left>
				<nb-text class="text">{{ docs.title }}</nb-text>
		  </nb-left>
		  <nb-body>
				<nb-text class="text">{{ formatDate(docs.created_at) }}</nb-text>
		  </nb-body>
		  <nb-right>
				<nb-icon class="text" :name="Platform.OS === 'android' ? 'eye' : 'download'" />
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
import {fetchData} from "../utils/fetch";
import * as FileSystem from 'expo-file-system';
import * as IntentLauncher from 'expo-intent-launcher';
import * as Permissions from 'expo-permissions';
import {Platform} from 'react-native';
import * as MediaLibrary from 'expo-media-library';
import { Toast } from 'native-base';
import * as Sharing from 'expo-sharing';

export default {
  props: {
		navigation: {
			type: Object,
		}
  },
  data() {
		return {
			selectedDoc: '0',
			clientDocs: {},
			dataIsReady: false,
			formatDate,
			buttonOff: false,
			searchDocs:'',
			Platform
		};
  },
	computed: {
		getInput: function(){
			if(this.searchDocs.length >= 3 ||  this.searchDocs.length  === 0 ){
				fetchData(`consultant/doc/other-search?search=${this.searchDocs}&client_id=${this.navigation.getParam('id')}`,this.$root.user.token).then(val => {
					this.dataIsReady = true;
					this.clientDocs = val;
				});
			}
		}
	},
  components: { FooterNav,Header },
  methods: {
		goBack: function () {
			this.navigation.goBack();
		},
		showPDF: async function(id,clientID,titleDoc){
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
						FileSystem.documentDirectory + `${titleDoc}.pdf`,options
				).then(async({ uri,status }) => {
						FileSystem.getContentUriAsync(uri).then(cUri => {
							IntentLauncher.startActivityAsync('android.intent.action.VIEW', {
								data: cUri,
								flags: 1,
							});
						});
				});
			}
				that.formLoaded = true;
		},
		downloadPDF: async function(id,clientID,titleDoc){
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
						FileSystem.documentDirectory + `${titleDoc.replace(/\s/g, '')}.pdf`,options
				).then(async({ uri,status }) => {
					if(this.Platform.OS === 'android'){
						const asset = await MediaLibrary.createAssetAsync(uri);
      			await MediaLibrary.createAlbumAsync("Download", asset, false);
						Toast.show({
							text: `uw document is succesvol opgeslagen. Ga naar uw bestandsbeheer/telefoonopslag voor uw download`,
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
		detailOther: function (id,clientID,docType,titleDoc) {
				if(docType === 'jpg'){
					this.navigation.navigate('OtherDocsDetails', {
						docID: id,
						ClientID:clientID,
						docType:docType
					});
				}else {
					if(this.Platform.OS === 'android'){
						this.showPDF(id,clientID,titleDoc);
					}else {
						this.downloadPDF(id,clientID,titleDoc);
					}
				}
		}
  },
};
</script>
