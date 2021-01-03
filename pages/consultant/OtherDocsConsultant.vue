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
        <nb-list-item v-for="docs in clientDocs" :key="docs.id">
          <nb-left>
            <nb-text class="text">{{ docs.title }}</nb-text>
          </nb-left>
          <nb-body>
            <nb-text class="text">{{ formatDate(docs.doc_date_time) }}</nb-text>
          </nb-body>
          <nb-right>
            <nb-button iconLeft transparent :on-press="() => detailOther(docs.id,docs.client_id)">
              <nb-icon class="text" name="arrow-forward" />
            </nb-button>
          </nb-right>
        </nb-list-item>
      </nb-list>
      <nb-card-item class="loadingWrapper" v-else>
			  <image :source="require('../../assets/images/loader.gif')" class="loading" />
	   </nb-card-item>
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

.loadingWrapper {
  align-items: center;
  justify-content: center;
  flex:1;
}

.loading {
  height:50;
  width:50;
}
</style>
<script>
import FooterNav from '../../included/FooterConsultant';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {formatDate} from "../utils/dates";

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
      clientDocs: {},
       dataIsReady: false,
       formatDate
    };
  },
  created() {
    this.getOtherDocs();
  },
  components: { FooterNav },
  methods: {
    getOtherDocs: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch(`http://api.arsus.nl/consultant/doc/others?client_id=${this.navigation.getParam('id')}`, {
          method: 'GET',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.user.token}`
          },
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          this.clientDocs = responseJson.results;
          this.dataIsReady = true;
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
    detailOther: function (id,clientID) {
      this.isModalVisible = true;
      console.log(clientID);
      this.navigation.navigate('OtherDocsDetails', {
        docID: id,
        ClientID:clientID
      });
    },
  },
};
</script>
