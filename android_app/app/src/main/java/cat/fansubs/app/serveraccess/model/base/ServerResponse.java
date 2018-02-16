package cat.fansubs.app.serveraccess.model.base;

import java.util.List;

public class ServerResponse<T> {
    private String status;
    private List<T> result;
    private ServerError error;

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public List<T> getResult() {
        return result;
    }

    public void setResult(List<T> result) {
        this.result = result;
    }

    public ServerError getError() {
        return error;
    }

    public void setError(ServerError error) {
        this.error = error;
    }
}
