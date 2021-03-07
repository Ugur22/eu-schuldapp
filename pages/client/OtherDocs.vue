<template>
  <nb-container>
    	<header :pageTitle="$root.lang.t('other_documents')" :method="goBack" />
    <nb-content  >
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
				<nb-list-item v-for="docs in clientDocs" :key="docs.id" :disabled="buttonOff" :on-press="() => detailOther(docs.id,docs.client_id,docs.file.filetype)">
          <nb-left>
            <nb-text class="text">{{ docs.title }}</nb-text>
          </nb-left>
          <nb-right>
            <nb-text class="text">{{ formatDate(docs.created_at) }}</nb-text>
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
    font-size:14;
}


</style>
<script>
import FooterNav from '../../included/Footer';
import Header from '../../included/Header';
import {formatDate} from "../utils/dates";
import {fetchData} from "../utils/fetch";
import * as Print from 'expo-print';
import AsyncStorage from '@react-native-async-storage/async-storage';


export default {
  props: {
    navigation: {
      type: Object,
    },
		user: {},
  },
	components: { FooterNav,Header },
  data() {
    return {
      selectedDoc: '0',
      clientDocs: {},
       dataIsReady: false,
       formatDate,
			 buttonOff: false,
			 searchDocs:''
    };
  },
	computed: {
		getInput: function(){
				if(this.searchDocs.length >= 3 ||  this.searchDocs.length  === 0 ){
				fetchData(`client/docs/others/search?search=${this.searchDocs}`,this.$root.user.token).then(val => {
					this.dataIsReady = true;
					this.clientDocs = val;
				});
			}
		}
	},
  methods: {
    goBack: function () {
      this.navigation.goBack();
		},
		showPDF: async function (id,clientID) {
			this.buttonOff = true;
			 setTimeout(() => this.buttonOff = false, 2000);

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
				let response = await fetch(`http://api.arsus.nl/document/pdf-download?client_id=${clientID}
				&document_id=${id}`, {
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
					Print.printAsync({uri:responseJson});
				} else {
					console.log(responseJson);
				}
			} catch (error) {
			console.log(error);
			console.error(error);
			}
		},
	detailOther: function (id,clientID,docType) {
			if(docType === 'jpg'){
				this.navigation.navigate('OtherDocsDetails', {
					docID: id,
					ClientID:clientID,
					docType:docType
				});
			}else {
				this.showPDF(id,clientID);
			}
		}
  },
};
</script>
