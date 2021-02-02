<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('my_account')" :method="goBack" />
    <nb-content padder>
      <nb-card v-if="dataIsReady">
        <nb-card-item
          header
          bordered
          :style="styles.center">
          <nb-text :style="styles.header"
            >{{clientData.firstname}} {{clientData.lastname}}</nb-text >
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-card-item >
              <nb-left >
                <nb-icon :style="styles.text" name="person"></nb-icon>
                <nb-text :style="styles.text">{{ $root.lang.t('BSN') }}: {{clientData.social_security_id}}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="styles.text" name="pin"></nb-icon>
                <nb-text :style="styles.text">{{clientData.address}}{{ "\n" }}{{clientData.postal_code}} {{clientData.birth_place}}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="styles.text" name="mail"></nb-icon>
                <nb-text :style="styles.text">{{ clientData.user.email }}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="styles.text" name="call"></nb-icon>
                <nb-text :style="styles.text">{{clientData.phonenumber}}</nb-text>
              </nb-left>
							<nb-right>
							</nb-right>
            </nb-card-item>
          </nb-body>
        </nb-card-item>
        <nb-card-item footer> </nb-card-item>
      </nb-card>
        <nb-spinner color="#0078ae" v-else />
    </nb-content>
    <nb-footer>
      <footer-nav
        :style="styles.background"
        activeBtn="account"
      ></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<script>
import FooterNav from '../../included/Footer';
import Header from '../../included/Header';
import {fetchData} from "../utils/fetch";
import {styles} from '../styling/style';

export default {
  props: {
    navigation: {
      type: Object,
    }
  },
  components: { FooterNav,Header },
  data() {
    return {
     clientData: {},
			dataIsReady: false,
			styles
    };
  },
  created() {
	},
	mounted() {
		fetchData(`client`,this.$root.user.token).then(val => {
			this.dataIsReady = true;
			this.clientData = val;
			});
	},
  methods: {
    goBack: function () {
      this.navigation.goBack();
    },
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
  },
};
</script>
