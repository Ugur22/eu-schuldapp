<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('forms')" :method="goBack" />
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
import Header from '../../included/Header';
import {formatDate} from "../utils/dates";
import * as Print from 'expo-print';
import {fetchData} from "../utils/fetch";

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
			buttonOff: false
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
		showPDF: async function (id,clientID) {
			this.isModalVisible = true;
			this.buttonOff = true;
			 setTimeout(() => this.buttonOff = false, 2000);

			try {
				let response = await fetch(`http://api.arsus.nl/document/pdf-download?client_id=${clientID}
				&document_id=${id}`,{
					method: 'GET',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.$root.user.token}`
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
    goBack: function () {
      this.navigation.goBack();
    },
  },
};
</script>
