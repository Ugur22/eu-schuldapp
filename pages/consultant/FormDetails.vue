<template>
  <nb-container>
	<nb-header :style="{ backgroundColor: '#0078ae' }">
	  <nb-left :style="{flex:1}">
		<nb-button transparent :on-press="goBack" >
		  <nb-icon name="arrow-back"/>
		</nb-button>
	  </nb-left>
	  <nb-body :style="{flex:1}">
	  	<nb-title>details</nb-title>
	  </nb-body>
	  <nb-right :style="{flex:1}">
		<nb-button transparent>
		  <nb-icon name="information-circle" />
		</nb-button>
	  </nb-right>
	</nb-header>
	<nb-content>
	  <nb-card v-if="dataIsReady" class="debtCard"
		:style="{
		  marginLeft: 10,
		  marginRight: 10,
		  marginTop: 10,
		  marginBottom: 10,
		  backgroundColor: '#0078ae'
		}"
	  >
		  <nb-body>
				<html-view :value="htmlDoc" :style="style" />
		  </nb-body>
	  </nb-card>
	<nb-spinner color="#0078ae" v-else /> 
	</nb-content>
  </nb-container>
</template>

<script>
import AsyncStorage from '@react-native-async-storage/async-storage';
import HTMLView from 'react-native-htmlview';

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
	  htmlDoc: {},
	  style: {
		backgroundColor:'white'
	  }
	};
  },
  created() {
	this.GetHTMl();
  },
  	components: { HTMLView },
  methods: {
	GetHTMl: async function () {
	  let value = '';
	  try {
		value = await AsyncStorage.getItem('login');
		this.user = JSON.parse(value); 
	  } catch (error) {
		// Error retrieving data
		console.log(error.message);
	  }

	  try {
		let response = await fetch(`http://api.arsus.nl/document/html-preview?document_id=${this.navigation.getParam('docID')}&client_id=${this.navigation.getParam('ClientID')}`, {
		  method: 'GET',
		  headers: {
			accept: 'application/json',
			'Content-Type': 'application/json',
			'Authorization': `Bearer ${this.user.token}`
		  }
		});

		let responseJson = await response.json();
		if (responseJson.success) {
		  this.htmlDoc = responseJson.results;
			// console.log(responseJson.results)
		  this.dataIsReady = true;
		} else {
		  console.log(responseJson);
		}
	  } catch (error) {
		console.log(error);
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
