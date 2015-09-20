package org.develnext.jphp.ext.javafx.classes.shape;

import javafx.scene.shape.Circle;
import org.develnext.jphp.ext.javafx.JavaFXExtension;
import php.runtime.annotation.Reflection.Name;
import php.runtime.annotation.Reflection.Property;
import php.runtime.annotation.Reflection.Signature;
import php.runtime.env.Environment;
import php.runtime.reflection.ClassEntity;

@Name(JavaFXExtension.NS + "shape\\UXCircle")
public class UXCircle extends UXShape<Circle> {
    interface WrappedInterface {
        @Property double radius();
    }

    public UXCircle(Environment env, Circle wrappedObject) {
        super(env, wrappedObject);
    }

    public UXCircle(Environment env, ClassEntity clazz) {
        super(env, clazz);
    }

    @Signature
    public void __construct() {
        __wrappedObject = new Circle();
    }

    @Signature
    public void __construct(double radius) {
        __wrappedObject = new Circle(radius);
    }

    @Override
    @Signature
    protected void setWidth(double v) {
        getWrappedObject().setRadius(v);
    }

    @Override
    protected void setHeight(double v) {
        getWrappedObject().setRadius(v);
    }

    @Override
    protected void setSize(double[] size) {
        if (size.length > 0) {
            getWrappedObject().setRadius(size[0]);
        }
    }
}
