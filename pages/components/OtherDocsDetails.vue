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
					:source="{uri: otherDoc
					}"/>
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
    };
  },
  created() {
		this.GetOther();
	},
  methods: {
    GetOther: async function () {
			let value = '';
			let that = this;
      try {
        value = await AsyncStorage.getItem('login');
				that.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
				let response = await fetch(`http://api.arsus.nl/document/file-download?client_id=${this.navigation.getParam('ClientID')}
				&document_id=${this.navigation.getParam('docID')}`, {
          method: 'GET',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.user.token}`
          }
        });

        let responseJson = await response.text();
        if (responseJson) {
					that.otherDoc = responseJson;
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
