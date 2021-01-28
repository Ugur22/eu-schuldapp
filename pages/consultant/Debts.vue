<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left  :style="{flex:1}">
        <nb-button transparent :on-press="goBack">
          <nb-icon name="arrow-back" />
        </nb-button>
      </nb-left>
      <nb-body  :style="{flex:1}">
        <nb-title>{{ $root.lang.t('debts') }}</nb-title>
      </nb-body>
      <nb-right  :style="{flex:1}">
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
        <nb-list-item v-for="debt in clientDebts" :key="debt.id" :on-press="() => detailDebt(debt.id,debt.client.id )">
          <nb-left>
            <nb-text  class="text">{{debt.debtor.name}}</nb-text>
          </nb-left>
          <nb-body>
            <nb-text class="text">{{ $root.lang.t('currency') }}{{debt.debt_amount}}</nb-text>
          </nb-body>
          <nb-right>
            <nb-icon class="text" name="arrow-forward" />
          </nb-right>
        </nb-list-item>
				<nb-list-item>
          <nb-left>
            <nb-text  class="text" :style="{ fontWeight: 'bold' }">Totale schuld:</nb-text>
          </nb-left>
          <nb-body>
            <nb-text :style="{ fontWeight: 'bold' }" class="text">{{ $root.lang.t('currency') }}{{totalDebts}}</nb-text>
          </nb-body>
          <nb-right>
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
import DebtDetailsClient from './DebtDetailsClient';
import {fetchData,getUser} from "../utils/fetch";

export default {
  props: {
		navigation: {
			type: Object,
		}
  },
  components: { FooterNav,DebtDetailsClient },
  data() {
    return {
			clientDebts: {},
			totalDebts:0,
			dataIsReady: false,
			userType: '',
			clientid:0,
			url:''
    };
  },
  created() {
	},
	mounted() {
		fetchData(`consultant/client/debts/?client_id=${ this.navigation.getParam('id')}`,this.$root.user.token).then(val => {
			let that = this;
			this.dataIsReady = true;
			this.clientDebts = val
			this.clientDebts.map(function(debt){
						that.totalDebts += parseFloat(debt.debt_amount);
					})
		;});
  },
  methods: {
    goBack: function () {
      this.navigation.goBack();
    },
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
		detailDebt: function (id,clientID) {
      this.navigation.navigate('DebtDetailsClient', {
        debtID: id,
        ClientID:clientID
      });
    },
  },
};
</script>

