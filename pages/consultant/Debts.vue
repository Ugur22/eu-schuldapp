<template>
  <nb-container>
    <header :pageTitle="$root.lang.t('debts')" :method="goBack" />
    <nb-content>
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input v-model="searchDebt" :placeholder="$root.lang.t('search')" />
      </nb-item>
			<nb-item>
				 <nb-text :style="{ display: 'none' }"  class="text">{{getInput}}</nb-text>
			</nb-item>
			<nb-item v-if="clientDebts.length === undefined && dataIsReady" >
					<nb-text class="text">
						geen resultaten gevonden
					</nb-text>
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
				<nb-list-item v-if="clientDebts.length !== undefined" >
          <nb-left>
            <nb-text  class="text" :style="{ fontWeight: 'bold' }">{{$root.lang.t('total_debt')}}:</nb-text>
          </nb-left>
          <nb-body >
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
import Header from '../../included/Header';
import DebtDetailsClient from './DebtDetailsClient';
import {fetchData} from "../utils/fetch";

export default {
  props: {
		navigation: {
			type: Object,
		}
  },
  components: { FooterNav,DebtDetailsClient,Header },
  data() {
    return {
			clientDebts: {},
			totalDebts:0,
			dataIsReady: false,
			userType: '',
			clientid:0,
			url:'',
			searchDebt:'',
    };
  },
	computed: {
		getInput: function(){
				if(this.searchDebt.length >= 3 ||  this.searchDebt.length  === 0 ){
					fetchData(`consultant/client/debts/search?search=${this.searchDebt}&client_id=${this.navigation.getParam('id')}`,this.$root.user.token).then(val => {
						this.dataIsReady = true;
						let that = this;
						that.clientDebts = val;
						that.totalDebts = 0;
						if(that.clientDebts.length > 0){
							that.clientDebts.map(function(debt){
										that.totalDebts += parseFloat(debt.debt_amount);
									})
							}
					});
			}
		}
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

