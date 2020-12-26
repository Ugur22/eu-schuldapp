<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left>
        <nb-button transparent>
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>{{ $root.lang.t('creditors_documents') }}</nb-title>
      </nb-body>
      <nb-right>
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content>
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input placeholder="zoek schuldeiser documenten" />
      </nb-item>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="collector in clientCollectors" :key="collector.ID">
          <nb-left>
            <nb-text class="text">{{collector.title}}</nb-text>
          </nb-left>
          <nb-right>
            <nb-text class="text">{{collector.doc_date_time.slice(0,11)}}</nb-text>
          </nb-right>
        </nb-list-item>
      </nb-list>
      <nb-card-item class="loadingWrapper" v-else>
			  <image :source="require('../assets/images/loader.gif')" class="loading" />
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
  font-size: 14;
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
import FooterNav from '../included/Footer';
import { AsyncStorage } from 'react-native';

export default {
  props: {
    navigation: {
      type: Object,
    },
    user: {},
  },
    created() {
    this.userData();
  },
  components: { FooterNav },
  data() {
    return {
      selectedDoc: '0',
       clientCollectors: {},
       dataIsReady: false
    };
  },
  methods: {
        userData: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch('http://api.arsus.nl/client/docs/debtors', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.user.email,
            password: this.user.password,
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
