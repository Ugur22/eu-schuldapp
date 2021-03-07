<template>
  <nb-container>
    	<header :pageTitle="$root.lang.t('name_creditor')" :method="goBack" />
    <nb-content>
      <nb-list v-if="dataIsReady">
        <nb-list-item v-for="collector in clientCollectors" :key="collector.id" :on-press="() => detailCollector(collector.id )">
          <nb-left>
            <nb-text class="text">{{collector.debtor}}</nb-text>
          </nb-left>
					<nb-right>
						<nb-icon class="text" name="arrow-forward" />
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
import {formatDate} from "../utils/dates";
import {fetchData} from '../utils/fetch';

export default {
  props: {
    navigation: {
      type: Object,
    }
  },
    created() {
	},
	mounted() {
		fetchData(`client/docs/debtors`,this.$root.user.token).then(val => {
			this.dataIsReady = true;
			this.clientCollectors = val;
		});
  },
  components: { FooterNav,Header },
  data() {
    return {
       clientCollectors: {},
       dataIsReady: false,
       formatDate
    };
  },
  methods: {
    goBack: function () {
      this.navigation.goBack();
    },
		detailCollector: function (debtorID) {
      this.navigation.navigate('CollectorDocsClient', {
        id: this.navigation.getParam('id'),
				debtorid:debtorID
      });
    }
  },
};
</script>
