<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left >
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body >
      	<nb-title>{{ $root.lang.t('creditors_documents') }}</nb-title>
      </nb-body>
    </nb-header>
    <nb-content>
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input placeholder="zoek schuldeiser documenten" />
      </nb-item>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="collector in clientCollectors" :key="collector.id">
          <nb-left>
            <nb-text class="text">{{collector.title}}</nb-text>
          </nb-left>
          <nb-body>
            <nb-text class="text">{{formatDate(collector.doc_date_time)}}</nb-text>
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
.headerText {
  color: white;
  font-weight: bold;
}
.detailText {
  color: white;
}
.marginBottom {
  margin-bottom: 20px;
}
.text {
  color: #0078ae;
}


</style>
<script>
import FooterNav from '../../included/Footer';
import {formatDate} from "../utils/dates";
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
			clientCollectors: {},
			dataIsReady: false,
			formatDate
		};
},
    created() {
	},
	mounted() {
		fetchData(`consultant/doc/debtors?client_id=${this.navigation.getParam('id')}`,this.$root.user.token).then(val => {
			this.dataIsReady = true;
			this.clientCollectors = val
			;});
  },
  components: { FooterNav },
  methods: {
    goBack: function () {
      this.navigation.goBack();
    },
  },
};
</script>
