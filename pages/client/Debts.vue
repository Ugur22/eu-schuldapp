<template>
  <nb-container>
    	<header :pageTitle="$root.lang.t('debts')" :method="goBack" />
    <nb-content>
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input placeholder="zoeken" />
      </nb-item>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="debt in clientDebts" :key="debt.id" :on-press="() => detailDebt(debt.id)">
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
            <nb-text  class="text" :style="{ fontWeight: 'bold' }">{{$root.lang.t('total_debt')}}:</nb-text>
          </nb-left>
          <nb-body>
            <nb-text :style="{ fontWeight: 'bold' }" class="text">{{ $root.lang.t('currency') }}{{totalDebts}}</nb-text>
          </nb-body>
          <nb-right>
            <nb-icon class="text" name="arrow-forward" />
          </nb-right>
        </nb-list-item>
      </nb-list>
			<!-- <nb-text v-if="dataIsReady && clientDebts.success == false">
				Er zijn nog geen schulden
			</nb-text> -->
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
import DebtDetails from './DebtDetails';
import {fetchData} from "../utils/fetch";

export default {
  props: {
    navigation: {
      type: Object,
    },
  },
  components: { FooterNav,DebtDetails,Header },
  data() {
    return {
			clientDebts: {},
			totalDebts:0,
      dataIsReady: false,
    };
  },
  created() {
	},
	mounted() {
		fetchData(`client/docs/debts`,this.$root.user.token).then(val => {
			let that = this;
			this.dataIsReady = true;
			this.clientDebts = val;
			if(this.clientDebts.length > 0){
				this.clientDebts.map(function(debt){
							that.totalDebts += parseFloat(debt.debt_amount);
						})
				}
			});
	},
  methods: {
    goBack: function () {
      this.navigation.goBack();
    },
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
    detailDebt: function (id) {
      this.isModalVisible = true;
      this.navigation.navigate('DebtDetails', {
        debtID: id
      });
    },
  },
};
</script>

