package cat.fansubs.app.serveraccess.model.base;

public class PiwigoResponse<T> {
    private String stat;
    private T result;

    public String getStat() {
        return stat;
    }

    public void setStat(String stat) {
        this.stat = stat;
    }

    public T getResult() {
        return result;
    }

    public void setResult(T result) {
        this.result = result;
    }
}
