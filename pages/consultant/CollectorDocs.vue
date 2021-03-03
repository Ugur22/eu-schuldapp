<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('creditors_documents')" :method="goBack" />
	<nb-content >
	  <nb-item :style="{ borderColor: '#62B1F6' }">
	  </nb-item>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="docs in collectorDocs" :key="docs.id" :on-press="() => showPDF(docs.id)">
          <nb-left>
            <nb-text class="text">{{docs.title}}</nb-text>
          </nb-left>
          <nb-body>
						<nb-text class="text"> {{formatDate(docs.updated_at)}}</nb-text>
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
		},
		user: {},
  },
  data() {
		return {
			selectedDoc: '0',
			collectorDocs: {},
			dataIsReady: false,
			formatDate,
			buttonOff: false
		};
  },
	mounted() {
		fetchData(`consultant/doc/debtors?client_id=${this.navigation.getParam('id')}`,this.$root.user.token).then(val => {
			this.dataIsReady = true;
			this.collectorDocs = val;
			this.collectorDocs  = this.collectorDocs.filter(debtor => (debtor.client_debt.debtor.id === this.navigation.getParam('debtorid')));
	});
  },
  components: { FooterNav,Header },
  methods: {
	goBack: function () {
	  this.navigation.goBack();
	},
	showPDF: async function (documentID) {
			this.buttonOff = true;
			 setTimeout(() => this.buttonOff = false, 2000);

			fetchContent(`document/pdf-download?client_id=${this.navigation.getParam('id')}&document_id=${documentID}`,this.$root.user.token).then(val => {
				Print.printAsync({uri:val});
				this.dataIsReady = true;
			});
		},
  },
};
</script>
