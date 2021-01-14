<template>
  <nb-container>
		<nb-header :style="{ backgroundColor: '#0078ae' }">
			<nb-left :style="{flex:1}">
			<nb-button transparent :on-press="goBack" >
				<nb-icon name="arrow-back"/>
			</nb-button>
			</nb-left>
			<nb-body :style="{flex:1}">
				<nb-title>detail</nb-title>
			</nb-body>
			<nb-right :style="{flex:1}">
			<nb-button transparent>
				<nb-icon name="information-circle" />
			</nb-button>
			</nb-right>
		</nb-header>
		<nb-text>
		</nb-text>
		<pdf-reader :style="{ padding: 0,margin:0 }" v-if="dataIsReady" :withPinchZoom="true" :withScroll="true"
		:source="{base64:formPDF}"
	/>
	<nb-spinner color="#0078ae" v-else /> 
	</nb-container>
</template>

<script>
import AsyncStorage from '@react-native-async-storage/async-storage';
import PDFReader from 'rn-pdf-reader-js'


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
			formPDF:''
		};
  },
  created() {
		this.getForm();
		
	},
	components: {PDFReader },
  methods: {
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
