<template>
  <nb-container>
	<nb-header :style="{ backgroundColor: '#0078ae' }">
	  <nb-left :style="{flex:1}">
		<nb-button transparent :on-press="goBack" >
		  <nb-icon name="arrow-back"/>
		</nb-button>
	  </nb-left>
	  <nb-body :style="{flex:1}">
	  	<nb-title>{{ $root.lang.t('clients') }}</nb-title>
	  </nb-body>
	  <nb-right :style="{flex:1}">
		<nb-button transparent>
		  <nb-icon name="information-circle" />
		</nb-button>
	  </nb-right>
	</nb-header>
	<nb-content >
	  <nb-item :style="{ borderColor: '#62B1F6' }">
		<nb-input placeholder="Search" />
	  </nb-item>
	  <nb-list v-if="dataIsReady">
	  <nb-list-item itemDivider class="list-Header">
		  <nb-left>
			<nb-text  class="text header-text">naam</nb-text>
		  </nb-left>
		  <nb-body>
			<nb-text  class="text header-text">status</nb-text>
		  </nb-body>
		  <nb-right>
		  </nb-right>
		</nb-list-item>
	  </nb-list>
	  <nb-list v-if="dataIsReady">
		<nb-list-item v-for="client in Clients" :key="client.id" >
		  <nb-left >
			<nb-text  class="text">{{client.firstname}} {{client.lastname}}</nb-text>
		  </nb-left>
		  <nb-body >
				<nb-button :disabled="client.status.status == 'Compleet' ? true : false" iconRight transparent :on-press="() => confirmNextStep(client.id)" >
					<nb-text  :class="client.status.status == 'Compleet' ? 'disabled': 'text'">{{client.status.status}}</nb-text>
					<nb-icon v-if="client.status.status !== 'Compleet'" name="arrow-dropright" :style="{ fontSize: 28, color: '#0078ae'}" />
				</nb-button>
		  </nb-body>
		  <nb-right :on-press="() => detailClient(client.id)">
				<nb-button iconRight transparent :on-press="() => detailClient(client.id)" >
				<nb-icon class="text" name="arrow-forward" />
			</nb-button>
		  </nb-right>
		</nb-list-item>
	  </nb-list>
	  <nb-spinner color="#0078ae" v-else /> 
	</nb-content>
	<nb-footer>
	  <footer-nav
		:style="{ backgroundColor: '#0078ae' }"
		activeBtn="clients"
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

.list-Header{
  padding-bottom: 0;
}

.header-text {
  font-weight: bold;
}

.disabled {
	color: grey;
}
</style>

<script>
import { Alert } from "react-native";
import FooterNav from '../../included/Footer';
import FileClient from './FileClient';
import {fetchData,PostData} from "../utils/fetch";

export default {
  props: {
	navigation: {
	  type: Object,
	},
	user: {},
  },
  components: { FooterNav,FileClient },
  data() {
		return {
			isModalVisible: false,
			Clients: {},
			dataIsReady: false,
			clientStatus: ''
		};
  },
  created() {
	},
	mounted() {
		fetchData(`consultant/clients`,this.$root.user.token).then(val => {
			this.dataIsReady = true;
			this.Clients = val;
		});
	},
  methods: {
		confirmNextStep: function(clientID){
					Alert.alert(
						'Weet u het zeker?',
						'bevestig volgende stap',
						[
							{
								text: 'Nee',
								style: 'cancel'
							},
							{ text: 'Ja', onPress: () => 
									PostData(`consultant/client/next-step?client_id=${clientID}`,this.$root.user.token).then(val => {
										fetchData(`consultant/clients`,this.$root.user.token).then(val => {
											this.dataIsReady = true;
											this.Clients = val;
									});
								})
							}
						],
						{ cancelable: false }
					);
		},
	goBack: function () {
	  this.navigation.goBack();
	},
	goToPage: function (page) {
	  this.navigation.navigate(page);
	},
	detailClient: function (id) {
	this.navigation.navigate('FileClient', {
		clientID: id
	  });
	},
  },
};
</script>

