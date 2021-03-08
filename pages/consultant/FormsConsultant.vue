<template>
  <nb-container>
      <header :pageTitle="$root.lang.t('forms')" :method="goBack" />
    <nb-content>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="form in clientForms" :key="form.id" :disabled="buttonOff" :on-press="() => showPDF(form.id,form.client_id,form.title)">
          <nb-left>
            <nb-text class="text">{{form.title}}</nb-text>
          </nb-left>
          <nb-body >
            <nb-text class="text">{{formatDate(form.created_at)}}</nb-text>
          </nb-body>
            <nb-right>
							<nb-button v-if="!form.main" iconRight transparent :on-press="() => signature(form.id,form.client_id,form.title)">
              	<nb-icon  class="text" name="create" />
							</nb-button>
							<nb-button v-else iconRight transparent >
              	<nb-icon  class="text" name="mail" />
							</nb-button>
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
}
</style>
<script>
import FooterNav from '../../included/Footer';
import {Dimensions,Platform} from 'react-native';
import Header from '../../included/Header';
import {formatDate} from "../utils/dates";
import * as Print from 'expo-print';
import {fetchData,fetchContent} from "../utils/fetch";

export default {
  props: {
    navigation: {
      type: Object,
    }
  },
  data() {
    return {
      clientForms: {},
      clientDocs: {},
      dataIsReady: false,
      formLoaded: false,
			formatDate,
			buttonOff: false,
			formHTML:'',
			formPDF:'',
    };
  },
  created() {
  },
 	mounted() {
		fetchData(`consultant/doc/forms?client_id=${this.navigation.getParam('id')}`,this.$root.user.token).then(val => {
			this.dataIsReady = true;
			this.clientForms = val;
			});
  },
  components: { FooterNav,Header},
  methods: {
    goBack: function () {
      this.navigation.goBack();
		},
		printToPdf: async function(htmlFile){
		let that = this;
		let width = Platform.OS === 'android' ? 480 : 612;
		let height = Platform.OS === 'android' ? 500 : 792;
		const response = await Print.printToFileAsync({html:htmlFile,width:width,height:height});
		that.formPDF = response.uri;
			that.formLoaded = true;
			if(that.formLoaded){
				Print.printAsync({uri:this.formPDF});
			}
		},
		showPDF: async function (id,clientID) {
			this.buttonOff = true;
			 setTimeout(() => this.buttonOff = false, 2000);

			fetchContent(`document/html-preview?client_id=${clientID}&document_id=${id}`,this.$root.user.token).then(val => {
				this.formHTML = val;
				this.printToPdf(this.formHTML);
			});
		},
		signature: function (id,clientID) {
			this.navigation.navigate('FormDetails', {
			docID: id,
			ClientID:clientID
			});
		},
  },
};
</script>
