<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
      	<nb-title>{{ $root.lang.t('forms') }}</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="form in clientForms" :key="form.id" :disabled="buttonOff" :on-press="() => showPDF(form.id,form.client_id)">
          <nb-left>
            <nb-text class="text">{{form.title}}</nb-text>
          </nb-left>
          <nb-right>
            <nb-text class="text">{{formatDate(form.created_at)}}</nb-text>
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
     font-size: 14;
}


</style>

<script>
import FooterNav from '../../included/Footer';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {formatDate} from "../utils/dates";
import { WebView } from 'react-native-webview';
import * as Print from 'expo-print';

export default {
  props: {
    navigation: {
      type: Object,
    },
    user: {},
  },
  data() {
    return {
      selectedDoc: '0',
      clientForms: {},
      clientDocs: {},
      dataIsReady: false,
			formatDate,
			buttonOff: false
    };
  },
  created() {
    this.getForms();
  },
  components: { FooterNav,"web-view": WebView },
  methods: {
		showPDF: async function (id,clientID) {
			this.isModalVisible = true;
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
    getForms: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch('http://api.arsus.nl/client/docs/forms', {
          method: 'GET',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
             'Authorization': `Bearer ${this.user.token}`
          },
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          this.clientForms = responseJson.results;
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
