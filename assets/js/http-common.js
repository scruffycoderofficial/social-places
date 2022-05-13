import axios from "axios";

/**
 * An HTTP Service that allows for interaction with the backend
 */
export default axios.create({
    baseURL: "http://localhost:9030/api",
    headers: {
        "Content-type": "application/json",
        "Access-Control-Allow-Origin": "*"
    }
});