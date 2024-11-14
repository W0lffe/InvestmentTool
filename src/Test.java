import javafx.stage.Stage;
import javafx.scene.Scene;

public class Test {

    private boolean testCase;

    private Test(boolean testCase) {
        this.testCase = testCase;
    }

    public boolean getTestCase() {
        return testCase;
    }

    private void setTestCase(boolean testCase) {
        this.testCase = testCase;
    }

    public static Test test = new Test(false);

    public static void TestMain(Stage primaryStage) {

        Container1 testCaseWindow = new Container1(10, "Test Case", "Run", "Go Back");

        testCaseWindow.getButton1().setOnAction(e -> {
            String test = TestCase(primaryStage);
            Interface info = new Interface(10, test);
            Utility.Delay();
            testCaseWindow.getChildren().add(info);
        });

        testCaseWindow.getButton2().setOnAction(e -> {
            primaryStage.setScene(Main.mainMenu);
        });

        Scene testScene = new Scene(testCaseWindow, 600, 400);
        primaryStage.setScene(testScene);
    }

    private static String TestCase(Stage primaryStage) {
        test.setTestCase(true);

        Investment investment1 = new Investment(12, 1.5f, 10000, "months", "month", 0, 0, "Linear");
        Investment investment2 = new Investment(1, 18f, 10000, "years", "year", 0, 0, "Linear");

        Funds fund1 = new Funds(12, 1.5f, 10000, "months", "month", 0, 0, "Funds/ETF");
        Funds fund2 = new Funds(1, 18f, 10000, "years", "year", 0, 0, "Funds/ETF");

        Stocks stock1 = new Stocks(12, 1.5f, 1008, "months", "month", 0, 0, "Stocks", 60);
        Stocks stock2 = new Stocks(1, 18f, 1008, "years", "year", 0, 0, "Stocks", 60);

        Calculations.Calculate(investment1, primaryStage);
        Calculations.Calculate(investment2, primaryStage);
        Calculations.Calculate(fund1, primaryStage);
        Calculations.Calculate(fund2, primaryStage);
        Calculations.Calculate(stock1, primaryStage);
        Calculations.Calculate(stock2, primaryStage);

        test.setTestCase(false);

        return "Test completed! Inspect calculations from file.";
    }
}
