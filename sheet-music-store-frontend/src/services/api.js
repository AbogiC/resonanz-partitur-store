import axios from "axios";

export const processCheckout = async (transactionData) => {
  try {
    const response = await axios.post("/create-transaction", transactionData);
    return response.data;
  } catch (error) {
    console.error("Error processing checkout:", error);
    throw error;
  }
};

export const processInputOrder = async (customerDetails) => {
  try {
    const response = await axios.post("/api/order", customerDetails, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    return response.data;
  } catch (error) {
    console.error("Error processing input order:", error);
    throw error;
  }
};