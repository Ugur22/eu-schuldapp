<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent :on-press="goBack" >
          <nb-icon :style="{color:'#fff'}" name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
      	<nb-title :style="{color:'#fff'}">{{$root.lang.t('details')}}</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
        <nb-button transparent>
          <nb-icon :style="{color:'#fff'}" name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content>
      <nb-card v-if="dataIsReady" transparent>
        <nb-card-item>
          <nb-body  v-for="debt in Debt" :key="debt.id">
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('Collector') }}</nb-text>
                <nb-text class="detailText">{{ debt.debtor.name }}</nb-text>
              </nb-col>
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('debt') }}</nb-text>
                <nb-text class="detailText">{{ $root.lang.t('currency') }}{{ debt.debt_amount }}</nb-text>
              </nb-col>
            </nb-grid>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('status') }}</nb-text>
                <nb-text class="detailText">{{ debt.status.status }}</nb-text>
              </nb-col>
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('total_repaid') }}</nb-text>
                <nb-text class="detailText">{{ $root.lang.t('currency') }}{{ debt.total_redeemed }}</nb-text>
              </nb-col>
            </nb-grid>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('repay_month') }}</nb-text>
                <nb-text class="detailText">{{ $root.lang.t('currency') }}{{ debt.redeem_per_month }}</nb-text>
              </nb-col>
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('preference') }}</nb-text>
                <nb-text class="detailText">{{ debt.preference }}</nb-text>
              </nb-col>
            </nb-grid>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">{{ $root.lang.t('months') }}</nb-text>
                <nb-text class="detailText">{{ debt.terms }}</nb-text>
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

export default {
  props: {
    navigation: {
      type: Object,
    },
  },
  data() {
    return {
      dataIsReady: false,
      Debt: {},
    };
  },
  created() {
	},
	mounted() {
		fetchData(`client/docs/debt?id=${this.navigation.getParam('debtID')}`,this.$root.user.token).then(val => {
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
.debtCard {
  border-radius: 15px;
}


</style>
