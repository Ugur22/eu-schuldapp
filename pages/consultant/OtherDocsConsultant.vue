<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('other_documents')" :method="goBack" />
	<nb-content >
	  <nb-item :style="{ borderColor: '#62B1F6' }">
		<nb-input placeholder="zoek overige documenten" />
	  </nb-item>
	  <nb-list v-if="dataIsReady">
		<nb-list-item v-for="docs in clientDocs" :key="docs.id" :on-press="() => detailOther(docs.id,docs.client_id,docs.file.filetype)">
		  <nb-left>
			<nb-text class="text">{{ docs.title }}</nb-text>
		  </nb-left>
		  <nb-body>
			<nb-text class="text">{{ formatDate(docs.created_at) }}</nb-text>
		  </nb-body>
		  <nb-right>
			<nb-icon class="text" name="arrow-forward" />
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
import {fetchData,fetchContent} from "../utils/fetch";
import * as Print from 'expo-print';

export default {
  props: {
		navigation: {
			type: Object,
		}
  },
  data() {
		return {
			selectedDoc: '0',
			clientDocs: {},
			dataIsReady: false,
			formatDate,
			buttonOff: false
		};
  },
  created() {
	},
	mounted() {
		fetchData(`consultant/doc/others?client_id=${this.navigation.getParam('id')}`,this.$root.user.token).then(val => {
			this.dataIsReady = true; this.clientDocs = val;
			});
  },
  components: { FooterNav,Header },
  methods: {
	goBack: function () {
	  this.navigation.goBack();
	},
	showPDF: async function (id,clientID) {
			this.buttonOff = true;
			 setTimeout(() => this.buttonOff = false, 2000);

			fetchContent(`document/pdf-download?client_id=${clientID}
				&document_id=${id}`,this.$root.user.token).then(val => {
				Print.printAsync({uri:val});
				this.dataIsReady = true;
			});
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
