<template>
  <nb-container>
     <header :pageTitle="$root.lang.t('signatures')" :method="goBack" />
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
		<nb-view color="#0078ae" v-if="singatureStatus == 'completed' " >
			<view :style="{   justifyContent: 'center', alignItems: 'center' }" >
				<nb-icon name="checkmark" :style="{ fontSize: 150, color: 'green' }" />
					<nb-text>{{$root.lang.t('singature_completed')}}</nb-text>
			</view>
		</nb-view> 
	</nb-container>
</template>

<script>
import SignatureScreen from 'react-native-signature-canvas';
import Header from '../../included/Header';
import axios from "axios";
import {fetchData} from "../utils/fetch";

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
	},
	components: {SignatureScreen,Header },
  	methods: {
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
