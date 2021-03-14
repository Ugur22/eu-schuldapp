<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('file')" :method="goBack" />
	<nb-content >
	  <nb-item :style="{ borderColor: '#62B1F6' }">
	  </nb-item>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="docs in collectorDocs" :key="docs.id" :on-press="() => showPDF(docs.id,docs.title)">
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
import {fetchData} from "../utils/fetch";
import * as FileSystem from 'expo-file-system';
import * as IntentLauncher from 'expo-intent-launcher';
import * as Permissions from 'expo-permissions';

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
		fetchData(`client/docs/debt-list`,this.$root.user.token).then(val => {
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
		showPDF: async function(id,title){
			this.buttonOff = true;
			setTimeout(() => this.buttonOff = false, 2000);
			const { status } = await Permissions.askAsync(Permissions.CAMERA_ROLL);
			let that = this;
				let options = {			
						headers: {
							accept: 'application/json',
							'Content-Type': 'application/json',
							'Authorization': `Bearer ${this.$root.user.token}`
				}}

			if (status === "granted") {
						FileSystem.downloadAsync(`http://api.arsus.nl/document/pdf-file?client_id=${this.navigation.getParam('id')}&document_id=${id}`,
						FileSystem.documentDirectory + `${title}.pdf`,options
				).then(async({ uri,status }) => {
						FileSystem.getContentUriAsync(uri).then(cUri => {
							IntentLauncher.startActivityAsync('android.intent.action.VIEW', {
								data: cUri,
								flags: 1,
							});
						});
				});
			}
				that.formLoaded = true;
		},
  },
};
</script>
