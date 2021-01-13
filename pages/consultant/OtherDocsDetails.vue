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
      <nb-card v-if="dataIsReady">
			<image
			:style="{width: null, height: 500,flex: 1}"
				:source="{uri: `http://api.arsus.nl/document/file-download?client_id=${navigation.getParam('ClientID')}
				&document_id=${navigation.getParam('docID')}&user=${user.email}&token=${token}`}"/>
      </nb-card>
   		 <nb-spinner color="#0078ae" v-else /> 
    </nb-content>
  </nb-container>
</template>
<script>
import AsyncStorage from '@react-native-async-storage/async-storage';

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
			otherDoc: '',
			token:''
    };
  },
  created() {
		// this.getToken();
	},
	 mounted() {
    this.getToken().then(val => {
			// this.GetOther(val);
    }).catch(e => {
      // error
      console.log(e);
		});
		
  },
  methods: {
		getToken: async function () {
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
		let response = await fetch(`http://api.arsus.nl/token`, {
		  method: 'POST',
		  headers: {
			'Content-Type': 'application/json',
			'Authorization': `Bearer ${this.user.token}`
		  }
		});

		let responseJson = await response.json();
		if (responseJson.success) {
			this.dataIsReady = true;
			that.token = responseJson.token;
			return responseJson.token;
		} else {
		  console.log(responseJson);
		}
	  } catch (error) {
		console.error(error);
	  }
	},
    GetOther: async function (token) {
			let value = '';
			let that = this;
      try {
        value = await AsyncStorage.getItem('login');
				this.user = JSON.parse(value);
				
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch(`http://api.arsus.nl/document/file-download?client_id=${this.navigation.getParam('ClientID')}&document_id=${this.navigation.getParam('docID')}&user=${this.user.email}&token=${token}`, {
          method: 'GET',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        });

        let responseJson = await response.text();
        if (responseJson.success) {
					that.otherDoc = responseJson;
					console.log(responseJson);
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
</style>
