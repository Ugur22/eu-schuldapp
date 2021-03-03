<template>
  <nb-container>
     <header :pageTitle="$root.lang.t('details')" :method="goBack" />
    <nb-content>
      <nb-card v-if="dataIsReady" transparent
        :style="{
          backgroundColor: '#0078ae'
        }"
      >
        <nb-card-item rounded >
          <nb-body>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('Collector') }}</nb-text>
                <nb-text class="detailText">{{Debt.debtor.name}}</nb-text>
              </nb-col>
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('debt') }}</nb-text>
                <nb-text class="detailText">{{ $root.lang.t('currency') }}{{ Debt.debt_amount }}</nb-text>
              </nb-col>
            </nb-grid>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('status') }}</nb-text>
                <nb-text class="detailText">{{Debt.status.status}}</nb-text>
              </nb-col>
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('total_repaid') }}</nb-text>
                <nb-text class="detailText">{{ $root.lang.t('currency') }}{{ Debt.total_redeemed }}</nb-text>
              </nb-col>
            </nb-grid>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('repay_month') }}</nb-text>
                <nb-text class="detailText">{{ $root.lang.t('currency') }}{{ Debt.redeem_per_month }}</nb-text>
              </nb-col>
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('preference') }}</nb-text>
                <nb-text class="detailText">{{ Debt.preference }}</nb-text>
              </nb-col>
            </nb-grid>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('months') }}</nb-text>
                <nb-text class="detailText">{{ Debt.terms }}</nb-text>
              </nb-col>
            </nb-grid>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('note') }}</nb-text>
                <nb-text class="detailText">{{ Debt.notes }}</nb-text>
              </nb-col>
            </nb-grid>
          </nb-body>
        </nb-card-item>
      </nb-card>
      <nb-spinner color="#0078ae" v-else /> 
    </nb-content>
  </nb-container>
</template>

<script>
import {fetchData} from "../utils/fetch";
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
			Debt: {},
    };
  },
  created() {
	},
	mounted() {
			fetchData(`consultant/client/debt/details?id=${this.navigation.getParam('debtID')}&client_id=${this.navigation.getParam('ClientID')}`,this.$root.user.token).then(val => {
			this.dataIsReady = true;
				this.Debt = val;
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
.headerText {
  color: #0078ae;
  font-weight: bold;
  font-size: 20px;
}
.detailText {
  color: #0078ae;
  font-size: 16px;
}

.marginBottom {
  margin-bottom: 20px;
}


</style>
