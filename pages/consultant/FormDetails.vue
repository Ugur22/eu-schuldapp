<template>
  <nb-container>
		<nb-header :style="{ backgroundColor: '#0078ae' }">
			<nb-left :style="{flex:1}">
			<nb-button transparent :on-press="goBack" >
				<nb-icon name="arrow-back"/>
			</nb-button>
			</nb-left>
			<nb-body :style="{flex:1}">
				<nb-title>handtekening</nb-title>
			</nb-body>
			<nb-right :style="{flex:1}">
			<nb-button transparent :on-press="toggleSignature">
				<nb-icon :name='enableSignature ? "close" : "create"' />
			</nb-button>
			</nb-right>
		</nb-header>
		<view  :style="{ justifyContent: 'center', alignItems: 'center',width: null, height: 200 }">
			<signature-screen
				descriptionText="client"
				clearText="opnieuw"
				confirmText="bevestig"
				:autoClear="true" 
				ref="useSignature"
				:webStyle="webStyle"
				:onOK="handleSignature"
				:onEnd="() => handleEnd('client')"
				imageType="image/jpg"/>
		</view>

	</nb-container>
</template>

<script>
import AsyncStorage from '@react-native-async-storage/async-storage';
import SignatureScreen from 'react-native-signature-canvas';
import * as Print from 'expo-print';
import { Toast } from 'native-base';
import axios from "axios";

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
			formPDF:'',
			signature:'',
			author:'',
			enableSignature:false,
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
  created() {
		this.CheckSignatures();
		
	},
	components: {SignatureScreen },
  methods: {
		toggleSignature: function () {
      this.enableSignature = !this.enableSignature;
		},
		handleEnd: function(author){
			this.author = author;
		},
		handleSignature: async function(signature,author) {
			this.signature = signature;
			// onOK(signature);
			let value = '';
      try {
        value = await AsyncStorage.getItem('login');
				this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }
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
					Authorization: `Bearer ${this.user.token}`,
				},
				}).then(function(response){
						console.log(response.data);
				});
			} catch (error) {
				console.error(error);
			}
		},
		CheckSignatures: async function() {
			let value = '';
      try {
        value = await AsyncStorage.getItem('login');
				this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }
			try {

			const response = await axios.get(`http://api.arsus.nl/document/signatures?document_id=${this.navigation.getParam('docID')}`,{
				headers: {
					Accept: 'application/json',
					'Content-type': 'application/json',
					Authorization: `Bearer ${this.user.token}`,
				},
				}).then(function(response){
						console.log(response.data);
				});
			} catch (error) {
				console.error(error);
			}
		},
		goBack: function () {
		this.navigation.goBack();
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
