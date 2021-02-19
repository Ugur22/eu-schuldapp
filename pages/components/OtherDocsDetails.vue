<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('details')" :method="goBack" />
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
import {fetchContent} from "../utils/fetch";
import Header from '../../included/Header';

export default {
  props: {
    navigation: {
      type: Object,
    },
    user: {},
  },
  components: {Header },
  data() {
    return {
      dataIsReady: false,
			otherDoc: '',
    };
  },
  created() {
	},
	mounted() {
		fetchContent(`document/file-download?client_id=${this.navigation.getParam('ClientID')}&document_id=${this.navigation.getParam('docID')}`,this.$root.user.token).then(val => {
			let that = this;
			this.dataIsReady = true;
      that.otherDoc = val;
			});
	},
  methods: {
    goBack: function () {
     this.navigation.goBack();
    },
  },
};
</script>
<style>
</style>
