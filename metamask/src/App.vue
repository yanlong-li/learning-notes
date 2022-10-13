<script setup>


import {ref} from "vue";
import detectEthereumProvider from '@metamask/detect-provider';

const logs = ref([]);

console.log = function (text) {
  logs.value.unshift(text);
}

/*****************************************/
/* Detect the MetaMask Ethereum provider */
/*****************************************/

// this returns the provider, or null if it wasn't detected
detectEthereumProvider().then(function (provider) {
  if (provider) {
    startApp(provider); // Initialize your app
  } else {
    console.log('Please install MetaMask!');
  }
});


function startApp(provider) {
  // If the provider returned by detectEthereumProvider is not the same as
  // window.ethereum, something is overwriting it, perhaps another wallet.
  if (provider !== window.ethereum) {
    console.error('Do you have multiple wallets installed?');
  }
  // Access the decentralized web!
  console.log('startApp')
}

/**********************************************************/
/* Handle chain (network) and chainChanged (per EIP-1193) */
/**********************************************************/

// const chainId = await ethereum.request({method: 'eth_chainId'});
// handleChainChanged(chainId);

ethereum.on('chainChanged', handleChainChanged);

function handleChainChanged(_chainId) {
  // We recommend reloading the page, unless you must do otherwise
  // window.location.reload();
  console.log(_chainId)
  currentChain.value = _chainId;
}

/***********************************************************/
/* Handle user accounts and accountsChanged (per EIP-1193) */
/***********************************************************/

let currentAccount = ref("");
let currentChain = ref("");

// Note that this event is emitted on page load.
// If the array of accounts is non-empty, you're already
// connected.
ethereum.on('accountsChanged', handleAccountsChanged);

// For now, 'eth_accounts' will continue to always return an array
function handleAccountsChanged(accounts) {
  console.log(accounts)
  if (accounts.length === 0) {
    // MetaMask is locked or the user has not connected any accounts
    console.log('Please connect to MetaMask.', accounts);
  } else if (accounts[0] !== currentAccount.value) {
    currentAccount.value = accounts[0];
    // Do any other work!
    console.log(currentAccount.value)
  }
}

/*********************************************/
/* Access the user's accounts (per EIP-1102) */
/*********************************************/

// You should only attempt to request the user's accounts in response to user
// interaction, such as a button click.
// Otherwise, you popup-spam the user like it's 1999.
// If you fail to retrieve the user's account(s), you should encourage the user
// to initiate the attempt.
// document.getElementById('connectButton', connect);

// While you are awaiting the call to eth_requestAccounts, you should disable
// any buttons the user can click to initiate the request.
// MetaMask will reject any additional requests while the first is still
// pending.
function connect() {
  ethereum
      .request({method: 'eth_requestAccounts'})
      .then(handleAccountsChanged)
      .catch((err) => {
        if (err.code === 4001) {
          // EIP-1193 userRejectedRequest error
          // If this happens, the user rejected the connection request.
          console.log('Please connect to MetaMask.');
        } else {
          console.error(err);
        }
      });
}

function getAccount() {
  ethereum
      .request({method: 'eth_accounts'})
      .then(handleAccountsChanged)
      .catch((err) => {
        // Some unexpected error.
        // For backwards compatibility reasons, if no accounts are available,
        // eth_accounts will return an empty array.
        console.error(err);
      });
}

function getChain() {
  ethereum.request({method: 'eth_chainId'}).then(handleChainChanged);
}


function clearLog() {
  logs.value = [];
}

</script>

<template>
  <div>
    <h1>MetaMask Connect Test</h1>

    <h3> Current Account: {{currentAccount}} </h3>
    <h3> Current Chain: {{currentChain}} </h3>
    <button @click="connect">
      Connect
    </button>
    &nbsp;&nbsp;
    <button @click="getAccount">
      GetAccount
    </button>
    &nbsp;&nbsp;
    <button @click="getChain">
      GetChain
    </button>
    &nbsp;&nbsp;
    <button @click="clearLog">
      Clear Log
    </button>
    <ul>
      <li v-for="(item,index) in logs">{{ item }}</li>
    </ul>
  </div>
</template>

<style>
button {
  border: 1px solid #535bf2;
}

button:focus {
  outline: none;
}

ul {
  height: 500px;
  overflow-y: auto;
}

ul li {
  border-bottom: 1px solid #535bf2;
}
</style>
