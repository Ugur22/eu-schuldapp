<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left>
        <nb-button transparent :on-press="goBack">
          <nb-icon name="arrow-back" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>{{ $root.lang.t('debts') }}</nb-title>
      </nb-body>
      <nb-right>
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content>
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input placeholder="Search" />
      </nb-item>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="debt in clientDebts" :key="debt.id">
          <nb-left>
            <nb-text  class="text">{{debt.debtor.name}}</nb-text>
          </nb-left>
          <nb-body>
            <nb-text class="text">{{ $root.lang.t('currency') }}{{debt.debt_amount}}</nb-text>
          </nb-body>
          <nb-right>
          <nb-button transparent :on-press="() => detailDebt(debt.id,debt.client.id)">
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
import Modal from 'react-native-modal';
import FooterNav from '../../included/Footer';
import AsyncStorage from '@react-native-async-storage/async-storage';
import DebtDetailsClient from './DebtDetailsClient';

export default {
  props: {
    navigation: {
      type: Object,
    },
    user: {},
  },
  components: { FooterNav,DebtDetailsClient },
  data() {
    return {
      isModalVisible: false,
      clientDebts: {},
      dataIsReady: false,
    };
  },
  created() {
    this.userData();
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
        let response = await fetch('http://api.arsus.nl/consultant/client/debts', {
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
          this.clientDebts = responseJson.results;
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
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
    detailDebt: function (id,clientID) {
      this.isModalVisible = true;
      this.navigation.navigate('DebtDetailsClient', {
        debtID: id,
        ClientID:clientID
      });
    },
  },
};
</script>

