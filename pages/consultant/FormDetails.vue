<template>
  <nb-container>
				<!-- <web-view v-if="formLoaded" :allowFileAccess="true"  :allowUniversalAccessFromFileURLs="true" :source="{uri:formPDF}"
		:style="{marginTop: 0}" /> -->
     <header :pageTitle="$root.lang.t('details')" :method="goBack" />
		<view v-if="Amountsignatures">
			<view  v-for="authors in Amountsignatures" :key="authors"  :style="{ justifyContent: 'center', alignItems: 'center',width: null, height: 200 }">
				<signature-screen
					:descriptionText="authors"
					:clearText="$root.lang.t('try_again')"
					:confirmText="$root.lang.t('confirm')"
					:autoClear="true" 
					ref="useSignature"
					:webStyle="webStyle"
					:onOK="(sign,author) => handleSignature(sign,authors)"
					imageType="image/jpg"/>
			</view>
		</view>
		<nb-spinner color="#0078ae" v-if="Amountsignatures == 0 && singatureStatus != 'completed' " /> 
		<nb-text color="#0078ae" v-if="singatureStatus == 'completed' " >
			{{$root.lang.t('singature_completed')}}
		</nb-text> 
		<!-- <pdf-reader v-if="formLoaded" :withPinchZoom="true" :withScroll="true"
			:source="{uri:formPDF}"
	/> -->
	</nb-container>
</template>

<script>
import SignatureScreen from 'react-native-signature-canvas';
import Header from '../../included/Header';
import axios from "axios";
import {fetchData,fetchContent} from "../utils/fetch";
import PDFReader from 'rn-pdf-reader-js';
import * as Print from 'expo-print';
import { WebView } from 'react-native-webview';

export default {
  props: {
	navigation: {
	  type: Object,
	},
	user: {},
	},
  data() {
		return {
			dataIsReady: false,
			token:'',
			formHTML:'',
			formPDF:'',
			signature:'',
			author:'',
			singatureStatus:'',
			Amountsignatures:0,
			formLoaded: false,
			webStyle: `
				.m-signature-pad--footer .save {
							font-size: 16px;
							background-color: #008551;
				}
				.m-signature-pad--footer .clear {
						font-size: 16px;
						background-color: #e74c3c;
				}
				.m-signature-pad--footer .description {
					color:#000;
				}`	
		};
	},
	mounted() {
		fetchData(`document/signatures?document_id=${this.navigation.getParam('docID')}`,this.$root.user.token).then(val => {
			let that = this;
			that.singatureStatus = val.signature;
			that.Amountsignatures = val.need_signature_by;
			that.dataIsReady = true;
			});

			// this.getForm();
	},
	components: {SignatureScreen,Header,PDFReader,WebView },
  	methods: {
		// printToPdf: async function(htmlFile){
		// 	let that = this;
		// 	const response = await Print.printToFileAsync({html:htmlFile,width:480,height:500});
		// 	that.formPDF = response.uri;
		// 		that.formLoaded = true;
		// 		// if(that.formLoaded){
		// 		// 	Print.printAsync({uri:this.formPDF});
		// 		// }
		// },
		// getForm: async function() {
		// 	fetchContent(`document/html-preview?client_id=${this.navigation.getParam('ClientID')}&document_id=${this.navigation.getParam('docID')}`,this.$root.user.token).then(val => {
		// 		this.formHTML = val;
		// 		this.printToPdf(this.formHTML);
		// 	});
		// },
		handleSignature: async function(signature,author) {
			this.signature = signature;
			this.author = author;
			let that = this;

			try {

				let data = new FormData();
				
				data.append('signature', this.signature);
				data.append('document_id', this.navigation.getParam('docID'));
				data.append('client_id', this.navigation.getParam('ClientID'));
				data.append('author', this.author);

				
				const response = await axios.post('http://api.arsus.nl/consultant/sign',data,{
					headers: {
						Accept: 'application/json',
						'Content-type': 'multipart/form-data',
						Authorization: `Bearer ${that.$root.user.token}`,
					},
					}).then(function(response){

				fetchData(`document/signatures?document_id=${that.navigation.getParam('docID')}`,that.$root.user.token).then(val => {
						that.singatureStatus = val.signature;
						that.Amountsignatures = val.need_signature_by;
						that.dataIsReady = true;
						});
				});
			} catch (error) {
				console.error(error);
			}

		},
		goBack: function () {
			this.navigation.goBack();
		},
		goToPage: function (page) {
    	this.navigation.navigate(page);
      },
	},
};
</script>
<style>
.headerText {
  color: white;
  font-weight: bold;
  font-size: 20px;
}
.detailText {
  color: white;
  font-size: 16px;
}

.marginBottom {
  margin-bottom: 20px;
}
.debtCard {
  border-radius: 15px;
}
</style>
