<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('forms')" :method="goBack" />
    <nb-content>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="form in clientForms" :key="form.id" :disabled="buttonOff" :on-press="() => showPDF(form.id,form.client_id)">
          <nb-left>
            <nb-text class="text">{{form.title}}</nb-text>
          </nb-left>
					<nb-body>
            <nb-text class="text">{{formatDate(form.created_at)}}</nb-text>
					</nb-body>
					<nb-right>
						<nb-button v-if="!form.main" iconRight transparent>
							<nb-icon  class="text" name="document" />
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
import Header from '../../included/Header';
import {formatDate} from "../utils/dates";
import * as Print from 'expo-print';
import {fetchData,fetchContent} from "../utils/fetch";

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
      dataIsReady: false,
			formatDate,
			buttonOff: false,
			formHTML:'',
			formPDF:'',
			formLoaded:false
    };
  },
  created() {
	},
	mounted() {
		fetchData(`client/docs/forms`,this.$root.user.token).then(val => {
		this.dataIsReady = true;
			this.clientForms = val;
			});
	},
  components: { FooterNav,Header},
  methods: {
	printToPdf: async function(htmlFile){
		let that = this;
		const response = await Print.printToFileAsync({html:htmlFile,width:480,height:500});
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
    goBack: function () {
      this.navigation.goBack();
    },
  },
};
</script>
