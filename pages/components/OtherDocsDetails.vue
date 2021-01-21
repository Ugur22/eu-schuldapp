<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
      	<nb-title>details</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
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
      dataIsReady: false,
			otherDoc: '',
    };
  },
  created() {
	},
	mounted() {
		fetchData(`document/file-download?client_id=${this.navigation.getParam('ClientID')}
				&document_id=${this.navigation.getParam('docID')}`,'file').then(val => {
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
