<template>
  <nb-container>
		<nb-header :style="{ backgroundColor: '#0078ae' }">
			<nb-left :style="{flex:1}">
			<nb-button transparent :on-press="goBack" >
				<nb-icon name="arrow-back"/>
			</nb-button>
			</nb-left>
			<nb-body :style="{flex:1}">
				<nb-title>{{navigation.getParam('title')}}</nb-title>
			</nb-body>
			<nb-right :style="{flex:1}">
			<nb-button transparent :on-press="toggleSignature">
				<nb-icon :name='enableSignature ? "close" : "create"' />
			</nb-button>
			</nb-right>
		</nb-header>
		<view v-if="enableSignature" :style="{ justifyContent: 'center', alignItems: 'center',width: null, height: 200 }">
			<signature-screen
				descriptionText="Plaats uw handtekening"
				clearText="opnieuw"
				confirmText="bevestig"
				:autoClear="true" 
				ref="useSignature"
				:webStyle="webStyle"
				:onOK="handleSignature"
				imageType="image/jpeg"/>
		</view>
		<pdf-reader :style="{ padding: 0,margin:0 }" v-if="dataIsReady" :withPinchZoom="true" :withScroll="true"
		:source="{base64:formPDF}"
	/>
	<nb-spinner color="#0078ae" v-else /> 
	</nb-container>
</template>

<script>
import AsyncStorage from '@react-native-async-storage/async-storage';
import PDFReader from 'rn-pdf-reader-js'
import SignatureScreen from 'react-native-signature-canvas';


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
		this.getForm();
		
	},
	components: {PDFReader,SignatureScreen },
  methods: {
		toggleSignature: function () {
      this.enableSignature = !this.enableSignature;
    },
		handleSignature: async function(signature) {
			this.signature = signature;
			// onOK(signature);
			let value = '';
      try {
        value = await AsyncStorage.getItem('login');
				this.user = JSON.parse(value);
				console.log(this.user);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }
			try {
				let response = await fetch('http://api.arsus.nl/client/sign', {
					method: 'POST',
					headers: {
						Accept: 'application/json',
						'Content-type': 'multipart/form-data',
						Authorization: `Bearer ${this.user.token}`,
					},
					body: this.createFormData(this.signature, {
						client_id: this.navigation.getParam('clientID'),
						document_id: this.navigation.getParam('docID'),
						// author: `${this.user.firstname} ${this.user.lastname}`,
						author: 1,
					}),
				});

				let responseJson = await response.json();
				console.log(this.signature);
				if (responseJson) {
					Toast.show({
						text: 'signature added',
					});
				} else {
					console.log(responseJson);
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
            ? file.uri
            : file.uri.replace('file://', ''),
        type: 'image/jpeg',
        name: 'signature', 
      });

      Object.keys(body).forEach((key) => {
        data.append(key, body[key]);
      });
      return data;
    },
		goBack: function () {
		this.navigation.goBack();
		},
		getForm: async function(token) {
			let that = this;
			let value = '';
			try {
			value = await AsyncStorage.getItem('login');
			this.user = JSON.parse(value); 
			} catch (error) {
			// Error retrieving data
			console.log(error.message);
			}

			try {
				let response = await fetch(`http://api.arsus.nl/document/pdf-download?client_id=${this.navigation.getParam('ClientID')}
				&document_id=${this.navigation.getParam('docID')}`, {
					method: 'GET',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.user.token}`
          },
				});

				let responseJson = await response.text();
				if (responseJson) {
					this.dataIsReady = true;
					that.formPDF = responseJson;
				} else {
					console.log(responseJson);
				}
			} catch (error) {
			console.log(error);
			console.error(error);
			}
		}
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
