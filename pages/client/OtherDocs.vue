<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left >
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body >
      	<nb-title>{{ $root.lang.t('other_documents') }}</nb-title>
      </nb-body>
    </nb-header>
    <nb-content  >
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input placeholder="zoek overige documenten" />
      </nb-item>
      <nb-list v-if="dataIsReady">
				<nb-list-item v-for="docs in clientDocs" :key="docs.id" :on-press="() => detailOther(docs.id,docs.client_id)">
          <nb-left>
            <nb-text class="text">{{ docs.title }}</nb-text>
          </nb-left>
          <nb-right>
            <nb-text class="text">{{ formatDate(docs.created_at) }}</nb-text>
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
    font-size:14;
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
    }
  },
  data() {
    return {
      selectedDoc: '0',
      clientDocs: {},
       dataIsReady: false,
       formatDate
    };
  },
  created() {
	},
	mounted() {
		fetchData(`client/docs/others`,this.$root.user.token).then(val => {
			this.dataIsReady = true;
			this.clientDocs = val;
			});
	},
  components: { FooterNav },
  methods: {
    goBack: function () {
      this.navigation.goBack();
		},
		detailOther: function (id,clientID) {
			this.isModalVisible = true;
			this.navigation.navigate('OtherDocsDetails', {
			docID: id,
			ClientID:clientID
			});
		},
  },
};
</script>
