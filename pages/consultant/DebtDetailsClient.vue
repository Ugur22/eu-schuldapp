<template>
  <nb-container>
     <header :pageTitle="$root.lang.t('details')" :method="goBack" />
    <nb-content>
      <nb-card v-if="dataIsReady" transparent
        :style="{
          backgroundColor: '#0078ae'
        }"
      >
        <nb-card-item >
          <nb-body >
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
				<nb-button full info :style="styles.btn" iconRight :on-press="() => updateDebtStatus(Debt.client_id,Debt.id,Debt.status.id,Debt.status.status)" >
					<nb-text> complete step</nb-text>
					<nb-icon class="text" name="done-all" />
				</nb-button>
          </nb-body>
        </nb-card-item>
      </nb-card>
      <nb-spinner color="#0078ae" v-else /> 
    </nb-content>
  </nb-container>
</template>

<script>
import Header from '../../included/Header';
import {styles} from '../styling/style';
import {fetchData,PostData} from "../utils/fetch";
import { Alert } from "react-native";
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
			styles,
			DebtStatus:'',
			nextStep: '',
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
    updateDebtStatus: function (clientId,DebtId,StatusId,currentStatus) {
				fetchData(`consultant/client/debt/next-steps?client_id=${clientId}&debt_id=${DebtId}`,this.$root.user.token).then(val => {
					let that = this;
					val.map(function(status){
						if(status.sort === StatusId+1){
							that.nextStep = status.status;
						}
					})
					Alert.alert(
					`${this.$root.lang.t('confirm_message')}`,
					`Current status: ${currentStatus}\nNext status: ${this.nextStep}`,
					[
						{
							text: `${this.$root.lang.t('no')}`,
							style: 'cancel'
						},
						{ text: `${this.$root.lang.t('yes')}`, onPress: () => 
								fetchData(`consultant/client/debt/next-step?status_id=${StatusId}&debt_id=${DebtId}&client_id=${clientId}`,this.$root.user.token).then(val => {
									console.log(val);
									fetchData(`consultant/client/debt/details?id=${this.navigation.getParam('debtID')}&client_id=${this.navigation.getParam('ClientID')}`,this.$root.user.token).then(val => {
										this.dataIsReady = true;
										this.Debt = val;
									});
							})
						}
					],
					{ cancelable: false }
				);
			});
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
