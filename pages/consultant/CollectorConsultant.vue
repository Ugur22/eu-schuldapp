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
import FooterNav from '../../included/Footer';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {formatDate} from "../utils/dates";

export default {
  props: {
    navigation: {
      type: Object,
    },
    user: {},
  },
    created() {
    this.getCollectors();
  },
  components: { FooterNav },
  data() {
    return {
      selectedDoc: '0',
       clientCollectors: {},
       dataIsReady: false,
       formatDate
    };
  },
  methods: {
    getCollectors: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch('http://api.arsus.nl/consultant/doc/debtors', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.user.email,
            password: this.user.password,
            client_id: this.navigation.getParam('id'),
          }),
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          this.clientCollectors = responseJson.results;
          this.dataIsReady = true;
        } else {
          console.log(responseJson);
        }
      } catch (error) {
        console.error(error);
      }
    },
    goBack: function () {
      this.navigation.goBack();
    },
  },
};
</script>
