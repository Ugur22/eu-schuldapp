<template>
  <nb-container>
    <!-- <web-view  :source="{html:'<h1>Hello world</h1>'}" :style="{marginTop: 0}" /> -->
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
        <nb-list-item v-for="form in clientForms" :key="form.id">
          <nb-left>
            <nb-text class="text">{{form.title}}</nb-text>
          </nb-left>
          <nb-body>
            <nb-text class="text">{{formatDate(form.doc_date_time)}}</nb-text>
          </nb-body>
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
}


</style>

<script>
import FooterNav from '../../included/FooterConsultant';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {formatDate} from "../utils/dates";
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
      selectedDoc: '0',
      clientForms: {},
      clientDocs: {},
      dataIsReady: false,
      formatDate,
    };
  },
  created() {
    this.getForms();
    // this.getDoc();
  },
  mounted() { 
    //    this.getDoc().then(val => {
    //   // got value here
    //   this.clientDocs = val;
    //   console.log(val)
    // }).catch(e => {
    //   // error
    //   console.log(e);
    // });
  },
  components: { FooterNav,"web-view": WebView },
  methods: {
    getDoc: async function () {
      let value = '';

      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }
      try {
        let response = await fetch('http://api.arsus.nl/document/html-preview', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.user.email,
            password: this.user.password,
            client_id:67,
            document_id:1490
          }),
        });
        let responseJson = await response.json();
        if (responseJson.success) {
          value = JSON.stringify(responseJson.results);
          // this.clientDocs = this.clientDocs.replace(/['"]+/g, '');
          // this.clientDocs = '<h1>Hello world</h1>';
          // console.log(this.clientDocs);
          value  = value.replace(/['"]+/g, '');
          value = `<h1>Hello world</h1>`;
             return value;
          this.dataIsReady = true;

       
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
        let response = await fetch(`http://api.arsus.nl/consultant/doc/forms?client_id=${this.navigation.getParam('id')}`, {
          method: 'GET',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.user.token}`
          }
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
