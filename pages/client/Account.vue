<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent  :on-press="goBack">
          <nb-icon name="arrow-back" />
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
        <nb-title>{{ $root.lang.t('my_account') }}</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
      <nb-right />
    </nb-header>
    <nb-content padder>
      <nb-card v-if="dataIsReady">
        <nb-card-item
          header
          bordered
          :style="{ flex: 1, justifyContent: 'center', alignItems: 'center' }">
          <nb-text :style="styles.header"
            >{{clientData.firstname}} {{clientData.lastname}}</nb-text >
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-card-item >
              <nb-left >
                <nb-icon :style="styles.icon" name="person"></nb-icon>
                <nb-text :style="styles.text">{{ $root.lang.t('BSN') }}: {{clientData.social_security_id}}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="styles.icon" name="pin"></nb-icon>
                <nb-text :style="styles.text">{{clientData.address}}{{ "\n" }}{{clientData.postal_code}} {{clientData.birth_place}}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="styles.icon" name="mail"></nb-icon>
                <nb-text :style="styles.text">{{ clientData.user.email }}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="styles.icon" name="call"></nb-icon>
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
        :style="styles.footer"
        activeBtn="account"
      ></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<script>
import FooterNav from '../../included/Footer';
import {fetchData} from "../utils/fetch";
import {styles} from '../styling/style';

export default {
  props: {
    navigation: {
      type: Object,
    }
  },
  components: { FooterNav },
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
